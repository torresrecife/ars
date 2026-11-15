<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\Support\BVAcesso\BVAcessoClient;
use App\Support\BVAcesso\UserSync;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/** Login único: manda para o BVAcesso e recebe a pessoa de volta já identificada. */
class BVAcessoController extends Controller
{
    public function redirect(BVAcessoClient $client): RedirectResponse
    {
        abort_unless(BVAcessoClient::enabled(), 404);

        return redirect()->away($client->authorizeUrl());
    }

    public function callback(Request $request, BVAcessoClient $client, UserSync $sync, AuthService $auth): RedirectResponse
    {
        abort_unless(BVAcessoClient::enabled(), 404);
        $oauth = $request->session()->pull('bvacesso.oauth');

        if (! $oauth || ! hash_equals($oauth['state'], (string) $request->query('state'))) {
            return $this->fail(__('The sign-in expired. Please try again.'));
        }
        if ($request->filled('error') || ! $request->filled('code')) {
            return $this->fail(__('BVAcesso did not authorize the sign-in.'));
        }

        try {
            $tokens = $client->exchangeCode((string) $request->query('code'), $oauth['verifier']);
            $response = $client->user($tokens['access_token']);
        } catch (ConnectionException|RequestException $e) {
            Log::warning('BVAcesso: falha na troca do código', ['erro' => $e->getMessage()]);

            return $this->fail(__('Unable to reach BVAcesso. Please try again shortly.'));
        }
        if ($response->status() === 403) {
            return $this->fail(__('Your account does not have access to ARS. Ask the administrator.'));
        }
        if (! $response->successful()) {
            return $this->fail(__('BVAcesso did not confirm your account. Please try again.'));
        }

        $user = $sync->sync($response->json());
        if (! $user) {
            return $this->fail(__('Your account is not registered in ARS. Ask the administrator.'));
        }
        if ($user->status_usu !== 'ATI') {
            return $this->fail(__('Your ARS account is inactive. Contact the administrator.'));
        }

        // Mesmo caminho do login próprio: Auth, sessão nova e o contexto (setor, clientes, regiões).
        $auth->storeUserSession($user->toArray());
        $request->session()->put('bvacesso.session', BVAcessoClient::sessionFrom($tokens));
        $auth->refreshUserAccess($user->id_usu);

        return redirect(url('index'));
    }

    private function fail(string $message): RedirectResponse
    {
        return redirect()->to(url('login').'?local=1')->withErrors(['username' => $message]);
    }
}
