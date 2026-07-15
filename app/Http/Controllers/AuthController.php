<?php

namespace App\Http\Controllers;

use App\Repositories\RegionRepository;
use App\Repositories\UserRepository;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * @var AuthService
     */
    protected $authService;

    public function __construct()
    {
        $legacyApp = require base_path('bootstrap/legacy_app.php');
        $connection = $legacyApp->db()->mysql();
        $table = config('auth.user_table', 'usuarios');

        $this->authService = new AuthService(
            new UserRepository($connection, $table),
            new RegionRepository()
        );
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
