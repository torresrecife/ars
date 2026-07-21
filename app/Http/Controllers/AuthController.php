<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * @var AuthService
     */
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function showLogin(Request $request)
    {
        return response()->view('auth.login', [
            'alerta' => (int) $request->query('alerta', 0),
        ] + $this->legacyViewUrls());
    }

    public function login(Request $request)
    {
        $user = $this->authService->attempt(
            (string) $request->input('username', ''),
            (string) $request->input('passwd', '')
        );

        if ($user === false) {
            return redirect()->to(url('login') . '?alerta=1');
        }

        if ($this->authService->requiresPasswordChange($user)) {
            return response()->view('auth.force-password', [
                'userId' => (int) $user['id_usu'],
            ] + $this->legacyViewUrls());
        }

        $this->authService->refreshUserAccess($user['id_usu']);

        return redirect(url('index'));
    }

    public function logout(Request $request)
    {
        $this->authService->clearUserSession();
        session()->invalidate();
        session()->regenerateToken();

        return redirect(url('login'));
    }

    public function updateOwnPassword(Request $request)
    {
        $currentUser = $this->authService->currentUser();
        if (empty($currentUser)) {
            return $this->apiJsonResponse(false, 'unauthenticated', 'Sessao expirada.', array(), 401);
        }

        $idUsuario = (int) $request->input('id_usu', 0);
        $novaSenha = (string) $request->input('senha_usu1', '');

        if ($idUsuario <= 0 || $novaSenha === '') {
            return $this->apiJsonResponse(false, 'invalid_payload', 'Dados invalidos.', array(), 422);
        }

        if ((int) $currentUser['id_usu'] !== $idUsuario) {
            return $this->apiJsonResponse(false, 'forbidden', 'Usuario invalido para alteracao da senha.', array(), 403);
        }

        if (!$this->authService->updatePasswordAndAccess($idUsuario, $novaSenha)) {
            return $this->apiJsonResponse(false, 'update_failed', 'Nao foi possivel alterar a senha.', array(), 409);
        }

        return $this->apiJsonResponse(true, 'updated', 'Senha alterada com sucesso.');
    }
    private function legacyBaseUrl($legacyUrl)
    {
        $directory = rtrim(str_replace('\\', '/', dirname((string) $legacyUrl)), '/');

        if ($directory === '' || $directory === '.') {
            return '';
        }

        return $directory;
    }

    private function legacyViewUrls()
    {
        $legacyIndexUrl = url('index');
        $legacyLoginUrl = url('login');

        return array(
            'legacyIndexUrl' => $legacyIndexUrl,
            'legacyLoginUrl' => $legacyLoginUrl,
            'legacyBaseUrl' => $this->legacyBaseUrl($legacyLoginUrl),
        );
    }
}
