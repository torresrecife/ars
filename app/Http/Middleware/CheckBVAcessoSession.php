<?php

namespace App\Http\Middleware;

use App\Services\AuthService;
use App\Support\BVAcesso\BVAcessoClient;
use App\Support\BVAcesso\UserSync;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Quem entrou pelo BVAcesso é conferido de tempos em tempos (BVACESSO_CHECK_MINUTES):
 * conta desativada, acesso retirado ou saída no BVAcesso desconectam aqui também.
 * Se o BVAcesso não responder, a pessoa continua trabalhando (confere de novo na próxima página).
 */
class CheckBVAcessoSession
{
    public function __construct(private BVAcessoClient $client, private UserSync $sync, private AuthService $auth) {}

    public function handle(Request $request, Closure $next): Response
    {
        $session = $request->session()->get('bvacesso.session');
        if (! $session || ! $request->user() || ! BVAcessoClient::enabled()) {
            return $next($request);
        }
        if (now()->getTimestamp() - ($session['checked_at'] ?? 0) < max(1, config('bvacesso.check_minutes')) * 60) {
            return $next($request);
        }

        $info = $this->client->check($session);
        $user = is_array($info) ? $this->sync->sync($info) : null;
        if ($info === false || (is_array($info) && (! $user || $user->status_usu !== 'ATI' || $user->id_usu !== $request->user()->id_usu))) {
            return $this->disconnect($request);
        }
        if ($user) {
            // Perfil trocado no BVAcesso vale já (o menu e as permissões leem a sessão).
            $this->auth->syncSessionContext($user->toArray());
            $session['checked_at'] = now()->getTimestamp();
        }
        $request->session()->put('bvacesso.session', $session);

        return $next($request);
    }

    private function disconnect(Request $request): Response
    {
        $this->auth->clearUserSession();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $message = __('Your session was ended in BVAcesso. Please sign in again.');
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['ok' => false, 'code' => 'unauthenticated', 'message' => $message], 401);
        }

        return redirect()->to(url('login').'?local=1')->withErrors(['username' => $message]);
    }
}
