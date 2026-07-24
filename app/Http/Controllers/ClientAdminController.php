<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ClientStoreRequest;
use App\Http\Requests\ClientUpdateRequest;
use App\Services\AuthService;
use App\Services\ClientAdminService;
use App\Services\MainPageService;
use App\Support\View;
use Illuminate\Http\Request;

class ClientAdminController extends Controller
{
	/** @var ClientAdminService */
	private $service;

	/** @var View */
	private $view;

	/** @var MainPageService */
	private $mainPageService;

	/** @var AuthService */
	private $authService;

	public function __construct(ClientAdminService $service, View $view, MainPageService $mainPageService, AuthService $authService)
	{
		$this->service = $service;
		$this->view = $view;
		$this->mainPageService = $mainPageService;
		$this->authService = $authService;
	}

	public function index()
	{
		return $this->view->render('clientes/index', $this->service->indexData());
	}

	public function createPage(Request $request)
	{
		return $this->renderShellPage($request, 'clientes/form', array_merge(
			$this->service->formData(),
			array(
				'pageTitle' => __('New Client'),
				'submitLabel' => __('Save'),
				'backUrl' => url('clientes'),
				'formAction' => route('clientes.store.page'),
				'formMethod' => 'POST',
			)
		));
	}

	public function editPage(Request $request, $id)
	{
		$payload = $this->service->editPayload((int) $id);
		if (!is_array($payload) || empty($payload['banco_id'])) {
			abort(404, __('Client not found.'));
		}

		return $this->renderShellPage($request, 'clientes/form', array_merge(
			$this->service->formData($payload),
			array(
				'pageTitle' => __('Edit Client'),
				'submitLabel' => __('Save'),
				'backUrl' => url('clientes'),
				'formAction' => route('clientes.update.page', (int) $id),
				'formMethod' => 'PATCH',
			)
		));
	}

	public function storePage(ClientStoreRequest $request)
	{
		$result = $this->service->create($request->all());
		if (!$result->isSuccess()) {
			return redirect()->back()->withInput()->withErrors(array(
				'form' => __('Operation failed.'),
			));
		}

		return redirect()->route('clientes')->with('status', __('Client created successfully.'));
	}

	public function updatePage(ClientUpdateRequest $request, $id)
	{
		$input = $request->all();
		$input['banco_id'] = (int) $id;
		$result = $this->service->update($input);
		if (!$result->isSuccess()) {
			return redirect()->back()->withInput()->withErrors(array(
				'form' => __('Operation failed.'),
			));
		}

		return redirect()->route('clientes')->with('status', __('Client updated successfully.'));
	}

	public function destroyPage($id)
	{
		$result = $this->service->delete((int) $id);

		return redirect()->route('clientes')->with(
			$result->isSuccess() ? 'status' : 'error',
			$result->isSuccess() ? __('Client deleted successfully.') : __('Operation failed.')
		);
	}

	public function show($id)
	{
		$data = $this->service->editPayload((int) $id);
		if (!is_array($data) || empty($data['banco_id'])) {
			return $this->apiJsonResponse(false, 'not_found', __('Client not found.'), array(), 404);
		}

		return $this->apiJsonResponse(true, 'loaded', __('Client loaded.'), $data);
	}

	public function store(ClientStoreRequest $request)
	{
		return $this->mapWriteResultToJson($this->service->create($request->all()), __('Client created successfully.'));
	}

	public function update(ClientUpdateRequest $request, $id)
	{
		$input = $request->all();
		$input['banco_id'] = (int) $id;

		return $this->mapWriteResultToJson($this->service->update($input), __('Client updated successfully.'));
	}

	public function destroy($id)
	{
		return $this->mapWriteResultToJson($this->service->delete((int) $id), __('Client deleted successfully.'));
	}

	private function renderShellPage(Request $request, $viewName, array $viewData)
	{
		$user = $this->authService->currentUser();
		if (empty($user)) {
			throw new \RuntimeException(__('User session not found.'));
		}

		$this->authService->syncSessionContext($user);
		$pageData = $this->mainPageService->build(array(
			'section' => 'clientes',
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
