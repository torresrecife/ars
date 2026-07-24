<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\WeekStoreRequest;
use App\Http\Requests\WeekUpdateRequest;
use App\Services\AuthService;
use App\Services\MainPageService;
use App\Services\WeekService;
use App\Support\MonthMap;
use App\Support\View;
use Illuminate\Http\Request;

class WeekController extends Controller
{
	/** @var WeekService */
	private $weekService;

	/** @var View */
	private $view;

	/** @var MainPageService */
	private $mainPageService;

	/** @var AuthService */
	private $authService;

	public function __construct(WeekService $weekService, View $view, MainPageService $mainPageService, AuthService $authService)
	{
		$this->weekService = $weekService;
		$this->view = $view;
		$this->mainPageService = $mainPageService;
		$this->authService = $authService;
	}

	public function index()
	{
		$data = $this->weekService->all();

		return $this->view->render('semanas/index', array(
			'weeks' => $data['weeks'],
			'search' => $data['search'],
			'months' => MonthMap::localized(),
		));
	}

	public function createPage(Request $request)
	{
		return $this->renderShellPage($request, 'semanas/form', array_merge(
			$this->weekService->formData(),
			array(
				'pageTitle' => __('New Week'),
				'submitLabel' => __('Save'),
				'backUrl' => url('semanas'),
				'formAction' => route('semanas.store.page'),
				'formMethod' => 'POST',
				'months' => MonthMap::localized(),
			)
		));
	}

	public function editPage(Request $request, $id)
	{
		$payload = $this->weekService->findById((int) $id);
		if (!$payload || empty($payload['semanas_id'])) {
			abort(404, __('Week not found.'));
		}

		return $this->renderShellPage($request, 'semanas/form', array_merge(
			$this->weekService->formData($payload),
			array(
				'pageTitle' => __('Edit Week'),
				'submitLabel' => __('Save'),
				'backUrl' => url('semanas'),
				'formAction' => route('semanas.update.page', (int) $id),
				'formMethod' => 'PATCH',
				'months' => MonthMap::localized(),
			)
		));
	}

	public function show($id)
	{
		$row = $this->weekService->findById((int) $id);
		if (!$row) {
			return $this->apiJsonResponse(false, 'not_found', __('Week not found.'), array(), 404);
		}

		return $this->apiJsonResponse(true, 'loaded', __('Week loaded.'), $row);
	}

	public function store(WeekStoreRequest $request)
	{
		return $this->mapWriteResultToJson($this->weekService->createFromRequest($request->all()), __('Week created successfully.'));
	}

	public function storePage(WeekStoreRequest $request)
	{
		$result = $this->weekService->createFromRequest($request->all());
		if ($result->isDuplicate()) {
			return redirect()->back()->withInput()->withErrors(array(
				'mes_sem' => __('Duplicate record.'),
			));
		}

		if (!$result->isSuccess()) {
			return redirect()->back()->withInput()->withErrors(array(
				'form' => __('Operation failed.'),
			));
		}

		return redirect()->route('semanas')->with('status', __('Week created successfully.'));
	}

	public function update(WeekUpdateRequest $request, $id)
	{
		$input = $request->all();
		$input['id_sem'] = (int) $id;

		return $this->mapWriteResultToJson($this->weekService->updateFromRequest($input), __('Week updated successfully.'));
	}

	public function updatePage(WeekUpdateRequest $request, $id)
	{
		$input = $request->all();
		$input['id_sem'] = (int) $id;
		$result = $this->weekService->updateFromRequest($input);
		if ($result->isDuplicate()) {
			return redirect()->back()->withInput()->withErrors(array(
				'mes_sem' => __('Duplicate record.'),
			));
		}

		if (!$result->isSuccess()) {
			return redirect()->back()->withInput()->withErrors(array(
				'form' => __('Operation failed.'),
			));
		}

		return redirect()->route('semanas')->with('status', __('Week updated successfully.'));
	}

	public function destroy($id)
	{
		return $this->mapWriteResultToJson($this->weekService->delete((int) $id), __('Week deleted successfully.'));
	}

	public function destroyPage($id)
	{
		$result = $this->weekService->delete((int) $id);

		return redirect()->route('semanas')->with(
			$result->isSuccess() ? 'status' : 'error',
			$result->isSuccess() ? __('Week deleted successfully.') : __('Operation failed.')
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
			'section' => 'semanas',
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
