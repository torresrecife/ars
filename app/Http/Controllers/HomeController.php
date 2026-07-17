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
		return $this->webSectionPage($request, 'admin');
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
		$hasMetaContext = false;

		if (isset($input['startBanco']) && (int) $input['startBanco'] > 0) {
			$hasMetaContext = true;
		}

		if (isset($input['banco_id']) && (int) $input['banco_id'] > 0) {
			$hasMetaContext = true;
		}

		return $this->webSectionPage($request, $hasMetaContext ? 'metas-admin' : 'metas-select');
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
		$contentHtml = $this->contentRenderer->render($pageData->content(), $input, $session);
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
		$user = $this->authService->currentUser();
		if (empty($user)) {
			throw new \RuntimeException('Sessao de usuario nao encontrada.');
		}

		$this->authService->syncSessionContext($user);
	}
}
