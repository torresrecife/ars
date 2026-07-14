<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\MainPageService;
use App\Support\View;
use Illuminate\Http\Request;

class HomeController
{
	/** @var MainPageService */
	private $service;

	/** @var View */
	private $view;

	public function __construct(MainPageService $service, View $view)
	{
		$this->service = $service;
		$this->view = $view;
	}

	public function index(array $input, array $session)
	{
		$this->ensureLegacyEnvironment();

		return $this->renderPage($input, $session, 'index.php');
	}

	public function webIndex(Request $request)
	{
		$this->ensureLegacyEnvironment();

		$scriptName = str_replace('\\', '/', (string) $request->server('SCRIPT_NAME', ''));
		if ($scriptName === '') {
			$entryUrl = '/index.php';
		} else {
			$directory = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
			$entryUrl = ($directory === '' || $directory === '.')
				? '/index.php'
				: $directory . '/index.php';
		}

		return response($this->renderPage($request->all(), $_SESSION, $entryUrl));
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

	private function ensureLegacyEnvironment()
	{
		require_once base_path('inc/seguranca.php');
		require_once base_path('inc/functions.php');
		require_once base_path('inc/somadias.php');
		protegePagina(0);
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
			case 'user-***REMOVED***':
				return app(UserAdminController::class)->index();
			case 'sector-***REMOVED***':
				return app(SectorAdminController::class)->index();
			case 'client-***REMOVED***':
				return app(ClientAdminController::class)->index();
			case 'andamento-***REMOVED***':
				return app(AndamentoAdminController::class)->index();
			case 'meta-***REMOVED***':
				return app(MetaController::class)->index($input, $_SESSION);
			case 'week-***REMOVED***':
				return app(WeekController::class)->index();
			case 'region-***REMOVED***':
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
		$hid_send = isset($input['hid_send']) ? $input['hid_send'] : '';
		$hid_area = isset($input['hid_area']) ? $input['hid_area'] : '';
		$hid_flag = isset($input['hid_flag']) ? $input['hid_flag'] : '';
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
