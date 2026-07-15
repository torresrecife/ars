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
        $legacyIndexUrl = $this->legacyIndexUrl($request);
        $legacyLoginUrl = $this->legacyLoginUrl($request);

        return response()->view('auth.login', [
            'alerta' => (int) $request->query('alerta', 0),
            'legacyIndexUrl' => $legacyIndexUrl,
            'legacyLoginUrl' => $legacyLoginUrl,
            'legacyBaseUrl' => $this->legacyBaseUrl($legacyLoginUrl),
        ]);
    }

    public function login(Request $request)
    {
        $user = $this->authService->attempt(
            (string) $request->input('username', ''),
            (string) $request->input('passwd', '')
        );

        if ($user === false) {
            return response('', 302)->header('Location', $this->legacyLoginUrl($request) . '?alerta=1');
        }

        if ($this->authService->requiresPasswordChange($user)) {
            $legacyIndexUrl = $this->legacyIndexUrl($request);
            $legacyLoginUrl = $this->legacyLoginUrl($request);

            return response()->view('auth.force-password', [
                'userId' => (int) $user['id_usu'],
                'legacyIndexUrl' => $legacyIndexUrl,
                'legacyLoginUrl' => $legacyLoginUrl,
                'legacyBaseUrl' => $this->legacyBaseUrl($legacyLoginUrl),
            ]);
        }

        $this->authService->refreshUserAccess($user['id_usu']);

        return response('', 302)->header('Location', $this->legacyIndexUrl($request));
    }

    public function logout(Request $request)
    {
        $this->authService->clearUserSession();
        session()->invalidate();
        session()->regenerateToken();

        return response('', 302)->header('Location', $this->legacyLoginUrl($request));
    }

    public function updateOwnPassword(Request $request)
    {
        $currentUser = $this->authService->currentUser();
        if (empty($currentUser)) {
            return $this->legacyTextResponse('2');
        }

        $idUsuario = (int) $request->input('id_usu', 0);
        $novaSenha = (string) $request->input('senha_usu1', '');

        if ($idUsuario <= 0 || $novaSenha === '') {
            return $this->legacyTextResponse('2');
        }

        if ((int) $currentUser['id_usu'] !== $idUsuario) {
            return $this->legacyTextResponse('2');
        }

        return $this->legacyTextResponse($this->authService->updatePasswordAndAccess($idUsuario, $novaSenha) ? '1' : '2');
    }

    private function legacyIndexUrl(Request $request)
    {
        $scriptName = str_replace('\\', '/', (string) $request->server('SCRIPT_NAME', ''));
        if ($scriptName === '') {
            return 'index.php';
        }

        $directory = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
        if ($directory === '' || $directory === '.') {
            return '/index.php';
        }

        return $directory . '/index.php';
    }

    private function legacyLoginUrl(Request $request)
    {
        $scriptName = str_replace('\\', '/', (string) $request->server('SCRIPT_NAME', ''));
        if ($scriptName === '') {
            return 'login.php';
        }

        $directory = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
        if ($directory === '' || $directory === '.') {
            return '/login.php';
        }

        return $directory . '/login.php';
    }

    private function legacyBaseUrl($legacyUrl)
    {
        $directory = rtrim(str_replace('\\', '/', dirname((string) $legacyUrl)), '/');

        if ($directory === '' || $directory === '.') {
            return '';
        }

        return $directory;
    }
}
