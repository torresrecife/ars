<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\AuthService;
use App\Services\MainPageService;
use App\Services\SectorAdminService;
use App\Support\View;
use Illuminate\Http\Request;

class SectorAdminController extends Controller
{
	/** @var SectorAdminService */
	private $service;

	/** @var View */
	private $view;

	/** @var MainPageService */
	private $mainPageService;

	/** @var AuthService */
	private $authService;

	public function __construct(SectorAdminService $service, View $view, MainPageService $mainPageService, AuthService $authService)
	{
		$this->service = $service;
		$this->view = $view;
		$this->mainPageService = $mainPageService;
		$this->authService = $authService;
	}

	public function index()
	{
		return $this->view->render('setores/index', $this->service->indexData());
	}

	public function createPage(Request $request)
	{
		return $this->renderShellPage($request, 'setores/form', array_merge(
			$this->service->formData(),
			array(
				'pageTitle' => __('New Sector'),
				'submitLabel' => __('Save'),
				'backUrl' => url('setores'),
				'formAction' => route('setores.store.page'),
				'formMethod' => 'POST',
			)
		));
	}

	public function editPage(Request $request, $id)
	{
		$payload = $this->service->editPayload((int) $id);
		if (!is_array($payload) || empty($payload['area_id'])) {
			abort(404, __('Sector not found.'));
		}

		return $this->renderShellPage($request, 'setores/form', array_merge(
			$this->service->formData($payload),
			array(
				'pageTitle' => __('Edit Sector'),
				'submitLabel' => __('Save'),
				'backUrl' => url('setores'),
				'formAction' => route('setores.update.page', (int) $id),
				'formMethod' => 'PATCH',
			)
		));
	}

	public function storePage(Request $request)
	{
		$request->validate(array(
			'area_nome' => 'required|string|max:255',
		));

		$result = $this->service->create($request->all());
		if ($result->isDuplicate()) {
			return redirect()->back()->withInput()->withErrors(array(
				'area_nome' => __('Duplicate record.'),
			));
		}

		if (!$result->isSuccess()) {
			return redirect()->back()->withInput()->withErrors(array(
				'form' => __('Operation failed.'),
			));
		}

		return redirect()->route('setores')->with('status', __('Sector created successfully.'));
	}

	public function updatePage(Request $request, $id)
	{
		$request->validate(array(
			'area_nome' => 'required|string|max:255',
		));

		$input = $request->all();
		$input['area_id'] = (int) $id;
		$result = $this->service->update($input);
		if ($result->isDuplicate()) {
			return redirect()->back()->withInput()->withErrors(array(
				'area_nome' => __('Duplicate record.'),
			));
		}

		if (!$result->isSuccess()) {
			return redirect()->back()->withInput()->withErrors(array(
				'form' => __('Operation failed.'),
			));
		}

		return redirect()->route('setores')->with('status', __('Field updated successfully.'));
	}

	public function destroyPage($id)
	{
		$result = $this->service->delete((int) $id);

		return redirect()->route('setores')->with(
			$result->isSuccess() ? 'status' : 'error',
			$result->isSuccess() ? __('Sector deleted successfully.') : __('Operation failed.')
		);
	}

	public function confirmDeletePage(Request $request, $id)
	{
		$payload = $this->service->editPayload((int) $id);
		if (!is_array($payload) || empty($payload['area_id'])) {
			abort(404, __('Sector not found.'));
		}

		return $this->renderShellPage($request, 'shared/confirm-delete', array(
			'pageTitle' => __('Delete Sector'),
			'message' => __('Review the selected sector before confirming permanent deletion.'),
			'itemName' => (string) $payload['area_nome'],
			'formAction' => route('setores.destroy.page', (int) $id),
			'backUrl' => url('setores'),
		));
	}

	public function show($id)
	{
		$data = $this->service->editPayload((int) $id);
		if (!is_array($data) || empty($data['area_id'])) {
			return $this->apiJsonResponse(false, 'not_found', __('Sector not found.'), array(), 404);
		}

		return $this->apiJsonResponse(true, 'loaded', __('Sector loaded.'), $data);
	}

	public function store(Request $request)
	{
		$request->validate(array(
			'area_nome' => 'required|string|max:255',
		));

		return $this->mapWriteResultToJson($this->service->create($request->all()), __('Sector created successfully.'));
	}

	public function update(Request $request, $id)
	{
		$request->validate(array(
			'area_nome' => 'required|string|max:255',
		));

		$input = $request->all();
		$input['area_id'] = (int) $id;

		return $this->mapWriteResultToJson($this->service->update($input), __('Field updated successfully.'));
	}

	public function destroy($id)
	{
		return $this->mapWriteResultToJson($this->service->delete((int) $id), __('Sector deleted successfully.'));
	}

	private function renderShellPage(Request $request, $viewName, array $viewData)
	{
		$user = $this->authService->currentUser();
		if (empty($user)) {
			throw new \RuntimeException(__('User session not found.'));
		}

		$this->authService->syncSessionContext($user);
		$pageData = $this->mainPageService->build(array(
			'section' => 'setores',
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
