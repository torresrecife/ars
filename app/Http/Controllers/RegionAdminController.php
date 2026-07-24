<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\RegionStoreRequest;
use App\Http\Requests\RegionUpdateRequest;
use App\Services\AuthService;
use App\Services\MainPageService;
use App\Services\RegionAdminService;
use App\Support\View;
use Illuminate\Http\Request;

class RegionAdminController extends Controller
{
	/** @var RegionAdminService */
	private $service;

	/** @var View */
	private $view;

	/** @var MainPageService */
	private $mainPageService;

	/** @var AuthService */
	private $authService;

	public function __construct(RegionAdminService $service, View $view, MainPageService $mainPageService, AuthService $authService)
	{
		$this->service = $service;
		$this->view = $view;
		$this->mainPageService = $mainPageService;
		$this->authService = $authService;
	}

	public function index()
	{
		return $this->view->render('regioes/index', $this->service->indexData());
	}

	public function createPage(Request $request)
	{
		return $this->renderShellPage($request, 'regioes/form', array_merge(
			$this->service->formData(),
			array(
				'pageTitle' => __('New Region'),
				'submitLabel' => __('Save'),
				'backUrl' => url('regioes'),
				'formAction' => route('regioes.store.page'),
				'formMethod' => 'POST',
			)
		));
	}

	public function editPage(Request $request, $id)
	{
		$payload = $this->service->editPayload((int) $id);
		if (!is_array($payload) || empty($payload['regiao_id'])) {
			abort(404, __('Region not found.'));
		}

		return $this->renderShellPage($request, 'regioes/form', array_merge(
			$this->service->formData($payload),
			array(
				'pageTitle' => __('Edit Region'),
				'submitLabel' => __('Save'),
				'backUrl' => url('regioes'),
				'formAction' => route('regioes.update.page', (int) $id),
				'formMethod' => 'PATCH',
			)
		));
	}

	public function storePage(RegionStoreRequest $request)
	{
		$result = $this->service->create($request->all());
		if ($result->isDuplicate()) {
			return redirect()->back()->withInput()->withErrors(array(
				'regiao_slug' => __('Duplicate record.'),
			));
		}

		if (!$result->isSuccess()) {
			return redirect()->back()->withInput()->withErrors(array(
				'form' => __('Operation failed.'),
			));
		}

		return redirect()->route('regioes')->with('status', __('Region created successfully.'));
	}

	public function updatePage(RegionUpdateRequest $request, $id)
	{
		$input = $request->all();
		$input['regiao_id'] = (int) $id;
		$result = $this->service->update($input);
		if ($result->isDuplicate()) {
			return redirect()->back()->withInput()->withErrors(array(
				'regiao_slug' => __('Duplicate record.'),
			));
		}

		if (!$result->isSuccess()) {
			return redirect()->back()->withInput()->withErrors(array(
				'form' => __('Operation failed.'),
			));
		}

		return redirect()->route('regioes')->with('status', __('Region updated successfully.'));
	}

	public function destroyPage($id)
	{
		$result = $this->service->delete((int) $id);

		return redirect()->route('regioes')->with(
			$result->isSuccess() ? 'status' : 'error',
			$result->isSuccess() ? __('Region deleted successfully.') : ($result->isLinkedUsers() ? __('There are users linked to this region.') : __('Operation failed.'))
		);
	}

	public function show($id)
	{
		$data = $this->service->editPayload((int) $id);
		if (!is_array($data)) {
			return $this->apiJsonResponse(false, 'not_found', __('Region not found.'), array(), 404);
		}

		return $this->apiJsonResponse(true, 'loaded', __('Region loaded.'), $data);
	}

	public function store(RegionStoreRequest $request)
	{
		return $this->mapWriteResultToJson($this->service->create($request->all()), __('Region created successfully.'));
	}

	public function update(RegionUpdateRequest $request, $id)
	{
		$input = $request->all();
		$input['regiao_id'] = (int) $id;

		return $this->mapWriteResultToJson($this->service->update($input), __('Region updated successfully.'));
	}

	public function destroy($id)
	{
		return $this->mapWriteResultToJson(
			$this->service->delete((int) $id),
			__('Region deleted successfully.'),
			null,
			null,
			__('There are users linked to this region.')
		);
	}

	private function renderShellPage(Request $request, $viewName, array $viewData)
	{
		$user = $this->authService->currentUser();
		if (empty($user)) {
			throw new \RuntimeException(__('User session not found.'));
		}

		$this->authService->syncSessionContext($user);
		$pageData = $this->mainPageService->build(array(
			'section' => 'regioes',
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
