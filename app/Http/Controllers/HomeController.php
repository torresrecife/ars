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

		return response($this->renderPage($request->all(), $request->session()->all(), url('index')));
	}

	public function webSectionPage(Request $request, $section)
	{
		$this->ensureLegacyEnvironment();

		$input = $request->all();
		$input['section'] = (string) $section;

		return response($this->renderPage($input, $request->session()->all(), url('index')));
	}

	public function webCarteiras(Request $request)
	{
		return $this->webSectionPage($request, 'carteiras');
	}

	public function webPainel(Request $request)
	{
		return $this->webSectionPage($request, 'painel');
	}

	public function webProducao(Request $request)
	{
		return $this->webSectionPage($request, 'producao');
	}

	public function webRelatorio(Request $request)
	{
		$input = $request->all();
		$section = (isset($input['geral']) && (string) $input['geral'] === '1')
			? 'relatorio-semanal'
			: 'relatorio-mensal';

		return $this->webSectionPage($request, $section);
	}

	public function webAdmin(Request $request)
	{
		return $this->webSectionPage($request, '***REMOVED***');
	}

	public function webUsuarios(Request $request)
	{
		return $this->webSectionPage($request, 'usuarios');
	}

	public function webSetores(Request $request)
	{
		return $this->webSectionPage($request, 'setores');
	}

	public function webClientes(Request $request)
	{
		return $this->webSectionPage($request, 'clientes');
	}

	public function webAndamentos(Request $request)
	{
		return $this->webSectionPage($request, 'andamentos');
	}

	public function webMetas(Request $request)
	{
		$input = $request->all();
		$hasMetaContext = isset($input['startBanco']) || isset($input['banco_id']) || isset($input['meta_mes']) || isset($input['meta_ano']);

		return $this->webSectionPage($request, $hasMetaContext ? 'metas-***REMOVED***' : 'metas-select');
	}

	public function webSemanas(Request $request)
	{
		return $this->webSectionPage($request, 'semanas');
	}

	public function webRegioes(Request $request)
	{
		return $this->webSectionPage($request, 'regioes');
	}

	public function health()
	{
		return response()->json(array(
			'status' => 'ok',
			'app' => config('app.name'),
			'framework' => app()->version(),
		));
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

		return '';
	}

	private function renderControllerContent($controllerName, array $input)
	{
		switch ($controllerName) {
			case 'dashboard-panel':
				return app(DashboardPanelController::class)->index($input, session()->all());
			case 'general-production-weekly':
				return app(GeneralProductionController::class)->weekly($input, session()->all());
			case 'general-production-monthly':
				return app(GeneralProductionController::class)->monthly($input, session()->all());
			case 'user-***REMOVED***':
				return app(UserAdminController::class)->index();
			case 'sector-***REMOVED***':
				return app(SectorAdminController::class)->index();
			case 'client-***REMOVED***':
				return app(ClientAdminController::class)->index();
			case 'andamento-***REMOVED***':
				return app(AndamentoAdminController::class)->index();
			case 'meta-***REMOVED***':
				return app(MetaController::class)->index($input, session()->all());
			case 'week-***REMOVED***':
				return app(WeekController::class)->index();
			case 'region-***REMOVED***':
				return app(RegionAdminController::class)->index();
			default:
				return '';
		}
	}

}
