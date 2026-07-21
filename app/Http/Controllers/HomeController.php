<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\AuthService;
use App\Services\MainPageContentRenderer;
use App\Services\MainPageService;
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

	/** @var MainPageContentRenderer */
	private $contentRenderer;

	public function __construct(
		MainPageService $service,
		View $view,
		AuthService $authService,
		MainPageContentRenderer $contentRenderer
	) {
		$this->service = $service;
		$this->view = $view;
		$this->authService = $authService;
		$this->contentRenderer = $contentRenderer;
	}

	public function index(array $input, array $session)
	{
		$this->ensureLegacyEnvironment();

		return $this->renderPage($input, $session, 'index.php');
	}

	public function webIndex(Request $request)
	{
		return $this->webPageResponse($request);
	}

	public function webSectionPage(Request $request, $section)
	{
		return $this->webPageResponse($request, (string) $section);
	}

	public function webRelatorio(Request $request)
	{
		return $this->webPageResponse($request, $this->resolveReportSection($request->all()));
	}

	public function webMetas(Request $request)
	{
		return $this->webPageResponse($request, $this->resolveMetasSection($request->all()));
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
		$contentHtml = $this->contentRenderer->render($pageData->content(), $input, $session);
		$exportPath = dirname(dirname(dirname(__DIR__))) . '/php2/exportar.php';

		return $this->view->render('index/shell', array(
			'pageData' => $pageData,
			'contentHtml' => $contentHtml,
			'exportPath' => $exportPath,
			'entryUrl' => $entryUrl,
		));
	}

	private function webPageResponse(Request $request, $section = null)
	{
		$this->ensureLegacyEnvironment();

		$input = $request->all();
		if ($section !== null && $section !== '') {
			$input['section'] = (string) $section;
		}

		return response($this->renderPage($input, $request->session()->all(), url('index')));
	}

	private function resolveReportSection(array $input)
	{
		return (isset($input['geral']) && (string) $input['geral'] === '1')
			? 'relatorio-semanal'
			: 'relatorio-mensal';
	}

	private function resolveMetasSection(array $input)
	{
		$startBanco = isset($input['startBanco']) ? (int) $input['startBanco'] : 0;
		$bankId = isset($input['banco_id']) ? (int) $input['banco_id'] : 0;

		return ($startBanco > 0 || $bankId > 0) ? 'metas-admin' : 'metas-select';
	}

	private function ensureLegacyEnvironment()
	{
		$user = $this->authService->currentUser();
		if (empty($user)) {
			throw new \RuntimeException(__('User session not found.'));
		}

		$this->authService->syncSessionContext($user);
	}
}
