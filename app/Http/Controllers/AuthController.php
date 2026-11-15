<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use App\Services\MainPageService;
use App\Support\BVAcesso\BVAcessoClient;
use App\Support\WriteResult;
use App\Support\View;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * @var AuthService
     */
    protected $authService;

    protected $mainPageService;

    protected $view;

    public function __construct(AuthService $authService, ?MainPageService $mainPageService = null, ?View $view = null)
    {
        $this->authService = $authService;
        $this->mainPageService = $mainPageService;
        $this->view = $view;
    }

    public function showLogin(Request $request)
    {
        // Com o login único ligado e sem o login próprio, a tela de login é a do BVAcesso.
        if (BVAcessoClient::enabled() && !config('bvacesso.local_login') && !$request->boolean('local')) {
            return redirect()->route('bvacesso.redirect');
        }

        return response()->view('auth.login', [
            'alerta' => (int) $request->query('alerta', 0),
            'sso' => BVAcessoClient::enabled(),
            'localLogin' => !BVAcessoClient::enabled() || config('bvacesso.local_login'),
        ] + $this->legacyViewUrls());
    }

    public function login(Request $request)
    {
        abort_if(BVAcessoClient::enabled() && !config('bvacesso.local_login'), 404);

        $user = $this->authService->attempt(
            (string) $request->input('username', ''),
            (string) $request->input('passwd', '')
        );

        if ($user === false) {
            return redirect()->to(url('login') . '?alerta=1');
        }

		if ($this->authService->temporaryPasswordIsExpired($user)) {
			$this->authService->clearUserSession();
			return redirect()->to(url('login') . '?alerta=1');
		}

        if ($this->authService->requiresPasswordChange($user)) {
			return $this->forcePasswordPage($request, (int) $user['id_usu']);
        }

        $this->authService->refreshUserAccess($user['id_usu']);

        return redirect(url('index'));
    }

	private function forcePasswordPage(Request $request, $userId)
	{
		if ($this->mainPageService === null || $this->view === null) {
			return response()->view('auth.force-password', array('userId' => (int) $userId));
		}

		$user = $this->authService->currentUser();
		$pageData = $this->mainPageService->build(array('section' => 'usuarios'), $request->session()->all());

		return response($this->view->render('index/shell', array(
			'pageData' => $pageData,
			'contentHtml' => $this->view->render('auth.force-password', array('userId' => (int) $userId)),
			'exportPath' => dirname(dirname(dirname(__DIR__))) . '/php2/exportar.php',
			'entryUrl' => url('index'),
		)));
	}

    public function logout(Request $request)
    {
        $viaSso = $request->session()->has('bvacesso.session');
        $this->authService->clearUserSession();
        session()->invalidate();
        session()->regenerateToken();

        // Entrou pelo BVAcesso: sai de lá também (e, com isso, dos outros sistemas).
        if ($viaSso && BVAcessoClient::enabled()) {
            return redirect()->away(app(BVAcessoClient::class)->logoutUrl(url('login')));
        }

        return redirect(url('login'));
    }

    public function showProfile(Request $request)
    {
		$currentUser = $this->authService->currentUser();
		if (empty($currentUser)) {
			return redirect()->route('login');
		}

		$pageData = $this->mainPageService->build(array('section' => 'profile'), $request->session()->all());
		return response($this->view->render('index/shell', array(
			'pageData' => $pageData,
			'contentHtml' => $this->view->render('auth/profile', array('user' => $currentUser)),
			'exportPath' => dirname(dirname(dirname(__DIR__))) . '/php2/exportar.php',
			'entryUrl' => url('index'),
		)));
	}

    public function updateOwnPassword(Request $request)
    {
        $currentUser = $this->authService->currentUser();
        if (empty($currentUser)) {
            return $this->apiJsonResponse(false, 'unauthenticated', __('Session expired.'), array(), 401);
        }

        $idUsuario = (int) $request->input('id_usu', 0);
        $novaSenha = (string) $request->input('senha_usu1', '');

        if ($idUsuario <= 0 || $novaSenha === '') {
            return $this->apiJsonResponse(false, 'invalid_payload', __('Invalid data.'), array(), 422);
        }

        if ((int) $currentUser['id_usu'] !== $idUsuario) {
            return $this->apiJsonResponse(false, 'forbidden', __('Invalid user for password change.'), array(), 403);
        }

        $result = $this->authService->updatePasswordAndAccess($idUsuario, $novaSenha);
        if (!$result instanceof WriteResult || !$result->isSuccess()) {
            return $this->apiJsonResponse(false, 'update_failed', __('Unable to change password.'), array(), 409);
        }

        return $this->apiJsonResponse(true, 'updated', __('Password changed successfully.'));
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
