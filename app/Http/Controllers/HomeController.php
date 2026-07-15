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

		return response($this->renderPage($request->all(), $request->session()->all(), $this->buildScriptUrl($request, 'index.php')));
	}

	public function webSectionPage(Request $request, $section)
	{
		$this->ensureLegacyEnvironment();

		$input = $request->all();
		$input['section'] = (string) $section;

		return response($this->renderPage($input, $request->session()->all(), $this->buildScriptUrl($request, 'index.php')));
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
			case 'user-admin':
				return app(UserAdminController::class)->index();
			case 'sector-admin':
				return app(SectorAdminController::class)->index();
			case 'client-admin':
				return app(ClientAdminController::class)->index();
			case 'andamento-admin':
				return app(AndamentoAdminController::class)->index();
			case 'meta-admin':
				return app(MetaController::class)->index($input, session()->all());
			case 'week-admin':
				return app(WeekController::class)->index();
			case 'region-admin':
				return app(RegionAdminController::class)->index();
			default:
				return '';
		}
	}

}
