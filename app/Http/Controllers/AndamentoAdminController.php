<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\AndamentoStoreRequest;
use App\Http\Requests\AndamentoUpdateRequest;
use App\Services\AuthService;
use App\Services\AndamentoAdminService;
use App\Services\MainPageService;
use App\Support\View;
use Illuminate\Http\Request;

class AndamentoAdminController extends Controller
{
	/** @var AndamentoAdminService */
	private $service;

	/** @var View */
	private $view;

	/** @var MainPageService */
	private $mainPageService;

	/** @var AuthService */
	private $authService;

	public function __construct(AndamentoAdminService $service, View $view, MainPageService $mainPageService, AuthService $authService)
	{
		$this->service = $service;
		$this->view = $view;
		$this->mainPageService = $mainPageService;
		$this->authService = $authService;
	}

	public function index()
	{
		return $this->view->render('andamentos/index', $this->service->indexData());
	}

	public function createPage(Request $request)
	{
		return $this->renderShellPage($request, 'andamentos/form', array_merge(
			$this->service->formData(),
			array(
				'pageTitle' => __('New Progress'),
				'submitLabel' => __('Save'),
				'backUrl' => url('andamentos'),
				'formAction' => route('andamentos.store'),
				'formMethod' => 'POST',
			)
		));
	}

	public function editPage(Request $request, $id)
	{
		$payload = $this->service->editPayload((int) $id);
		if (!is_array($payload) || empty($payload['anda_id'])) {
			abort(404, __('Progress not found.'));
		}

		return $this->renderShellPage($request, 'andamentos/form', array_merge(
			$this->service->formData($payload),
			array(
				'pageTitle' => __('Edit Progress'),
				'submitLabel' => __('Save'),
				'backUrl' => url('andamentos'),
				'formAction' => route('andamentos.update', (int) $id),
				'formMethod' => 'PATCH',
			)
		));
	}

	public function storePage(AndamentoStoreRequest $request)
	{
		$result = $this->service->create($request->all());
		if ($result->isDuplicate()) {
			return redirect()->back()->withInput()->withErrors(array(
				'nome' => __('Duplicate record.'),
			));
		}

		if (!$result->isSuccess()) {
			return redirect()->back()->withInput()->withErrors(array(
				'form' => __('Operation failed.'),
			));
		}

		return redirect()->route('andamentos')->with('status', __('Progress created successfully.'));
	}

	public function updatePage(AndamentoUpdateRequest $request, $id)
	{
		$input = $request->all();
		$input['anda_id'] = (int) $id;
		$result = $this->service->update($input);
		if ($result->isDuplicate()) {
			return redirect()->back()->withInput()->withErrors(array(
				'nome' => __('Duplicate record.'),
			));
		}

		if (!$result->isSuccess()) {
			return redirect()->back()->withInput()->withErrors(array(
				'form' => __('Operation failed.'),
			));
		}

		return redirect()->route('andamentos')->with('status', __('Progress updated successfully.'));
	}

	public function destroyPage($id)
	{
		$result = $this->service->delete((int) $id);

		return redirect()->route('andamentos')->with(
			$result->isSuccess() ? 'status' : 'error',
			$result->isSuccess() ? __('Progress deleted successfully.') : __('Operation failed.')
		);
	}

	public function show($id)
	{
		$payload = $this->service->editPayload((int) $id);
		if (!is_array($payload) || empty($payload['anda_id'])) {
			return $this->apiJsonResponse(false, 'not_found', __('Progress not found.'), array(), 404);
		}

		return $this->apiJsonResponse(true, 'loaded', __('Progress loaded.'), $payload);
	}

	public function store(AndamentoStoreRequest $request)
	{
		return $this->mapWriteResultToJson($this->service->create($request->all()), __('Progress created successfully.'));
	}

	public function update(AndamentoUpdateRequest $request, $id)
	{
		$input = $request->all();
		$input['anda_id'] = (int) $id;

		return $this->mapWriteResultToJson($this->service->update($input), __('Progress updated successfully.'));
	}

	public function destroy($id)
	{
		return $this->mapWriteResultToJson($this->service->delete((int) $id), __('Progress deleted successfully.'));
	}

	private function renderShellPage(Request $request, $viewName, array $viewData)
	{
		$user = $this->authService->currentUser();
		if (empty($user)) {
			throw new \RuntimeException(__('User session not found.'));
		}

		$this->authService->syncSessionContext($user);
		$pageData = $this->mainPageService->build(array(
			'section' => 'andamentos',
		), $request->session()->all());
		$exportPath = dirname(dirname(dirname(__DIR__))) . '/php2/exportar.php';

		return response($this->view->render('index/shell', array(
			'pageData' => $pageData,
			'contentHtml' => $this->view->render($viewName, $viewData),
			'exportPath' => $exportPath,
			'entryUrl' => url('index'),
		)));
	}
}
