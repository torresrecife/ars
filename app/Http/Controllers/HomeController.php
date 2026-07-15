<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\MainPageService;
use App\Services\AuthService;
use App\Support\View;
use Illuminate\Http\Request;

class HomeController extends Controller
{
	/** @var MainPageService */
	private $service;

	/** @var View */
	private $view;

	/** @var AuthService */
	private $authService;

	public function __construct(MainPageService $service, View $view, AuthService $authService)
	{
		$this->service = $service;
		$this->view = $view;
		$this->authService = $authService;
	}

	public function index(array $input, array $session)
	{
		$this->ensureLegacyEnvironment();

		return $this->renderPage($input, $session, 'index.php');
	}

	public function webIndex(Request $request)
	{
		$this->ensureLegacyEnvironment();

		return response($this->renderPage($request->all(), $_SESSION, $this->buildScriptUrl($request, 'index.php')));
	}

	public function webSectionPage(Request $request, $section)
	{
		$this->ensureLegacyEnvironment();

		$input = $request->all();
		$input['section'] = (string) $section;

		return response($this->renderPage($input, $_SESSION, $this->buildScriptUrl($request, 'index.php')));
	}

	private function renderPage(array $input, array $session, $entryUrl)
	{
		$pageData = $this->service->build($input, $session);
		$contentHtml = $this->renderContent($pageData['content'], $input);
		$exportPath = dirname(dirname(dirname(__DIR__))) . '/php2/exportar.php';

		return $this->view->render('index/shell', array(
			'pageData' => $pageData,
			'contentHtml' => $contentHtml,
			'exportPath' => $exportPath,
			'entryUrl' => $entryUrl,
		));
	}

	private function buildScriptUrl(Request $request, $scriptFile)
	{
		$scriptName = str_replace('\\', '/', (string) $request->server('SCRIPT_NAME', ''));
		if ($scriptName === '') {
			return '/' . ltrim((string) $scriptFile, '/');
		}

		$directory = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');

		return ($directory === '' || $directory === '.')
			? '/' . ltrim((string) $scriptFile, '/')
			: $directory . '/' . ltrim((string) $scriptFile, '/');
	}

	private function ensureLegacyEnvironment()
	{
		require_once base_path('inc/bootstrap.php');
		require_once base_path('inc/functions.php');
		require_once base_path('inc/somadias.php');

		$user = $this->authService->currentUser();
		if (empty($user)) {
			throw new \RuntimeException('Sessao de usuario nao encontrada.');
		}

		$this->authService->syncSessionContext($user);
	}

	private function renderContent(array $content, array $input)
	{
		if ($content['type'] === 'view') {
			return $this->view->render($content['view'], $content['data']);
		}

		if ($content['type'] === 'controller') {
			return $this->renderControllerContent($content['controller'], $input);
		}

		$html = '';
		if (!empty($content['spacer'])) {
			$html .= "<br><br><br><br><br>";
		}

		return $html . $this->renderLegacyInclude($content['file'], $input);
	}

	private function renderControllerContent($controllerName, array $input)
	{
		switch ($controllerName) {
			case 'dashboard-panel':
				return app(DashboardPanelController::class)->index($input, $_SESSION);
			case 'general-production-weekly':
				return app(GeneralProductionController::class)->weekly($input, $_SESSION);
			case 'general-production-monthly':
				return app(GeneralProductionController::class)->monthly($input, $_SESSION);
			case 'user-admin':
				return app(UserAdminController::class)->index();
			case 'sector-admin':
				return app(SectorAdminController::class)->index();
			case 'client-admin':
				return app(ClientAdminController::class)->index();
			case 'andamento-admin':
				return app(AndamentoAdminController::class)->index();
			case 'meta-admin':
				return app(MetaController::class)->index($input, $_SESSION);
			case 'week-admin':
				return app(WeekController::class)->index();
			case 'region-admin':
				return app(RegionAdminController::class)->index();
			default:
				return '';
		}
	}

	private function renderLegacyInclude($file, array $input)
	{
		$fullPath = base_path($file);
		if (!is_file($fullPath)) {
			return '';
		}

		$originalPost = $_POST;
		$originalRequest = $_REQUEST;
		$_POST = array_merge($_POST, $input);
		$_REQUEST = array_merge($_REQUEST, $input);
		$app = isset($GLOBALS['app']) ? $GLOBALS['app'] : null;
		$conexao4 = isset($GLOBALS['conexao4']) ? $GLOBALS['conexao4'] : null;
		$conexao1 = isset($GLOBALS['conexao1']) ? $GLOBALS['conexao1'] : null;
		$arrMonths = isset($GLOBALS['arrMonths']) ? $GLOBALS['arrMonths'] : array();
		$_SG = isset($GLOBALS['_SG']) ? $GLOBALS['_SG'] : array();
		$usu_setor = isset($_SESSION['usuarioSetor']) ? $_SESSION['usuarioSetor'] : 0;
		$usu_Cliente = isset($_SESSION['usuarioCliente']) ? $_SESSION['usuarioCliente'] : 0;
		$usu_nivel = isset($_SESSION['usuarioNivel']) ? $_SESSION['usuarioNivel'] : '';
		$usu_id = isset($_SESSION['usuarioID']) ? $_SESSION['usuarioID'] : 0;
		$mesano = isset($GLOBALS['arrMonths']) ? $GLOBALS['arrMonths'][(int) date('m')] . ' / ' . date('Y') : date('m') . ' / ' . date('Y');

		ob_start();
		try {
			include $fullPath;
			$output = (string) ob_get_clean();
		} catch (\Throwable $exception) {
			ob_end_clean();
			$output = "<div style='margin:40px;font-family:Arial,sans-serif;font-size:14px'>"
				. "<b>Erro ao carregar o modulo legado.</b><br><br>"
				. htmlspecialchars($exception->getMessage(), ENT_QUOTES, 'UTF-8')
				. "</div>";
		}

		$_POST = $originalPost;
		$_REQUEST = $originalRequest;

		return $output;
	}
}
