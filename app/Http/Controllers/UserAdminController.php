<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Services\AuthService;
use App\Services\MainPageService;
use App\Services\UserAdminService;
use App\Support\View;
use Illuminate\Http\Request;

class UserAdminController extends Controller
{
	/** @var UserAdminService */
	private $service;

	/** @var View */
	private $view;

	/** @var MainPageService */
	private $mainPageService;

	/** @var AuthService */
	private $authService;

	public function __construct(UserAdminService $service, View $view, MainPageService $mainPageService, AuthService $authService)
	{
		$this->service = $service;
		$this->view = $view;
		$this->mainPageService = $mainPageService;
		$this->authService = $authService;
	}

	public function index()
	{
		return $this->view->render('usuarios/index', $this->service->indexData());
	}

	public function createPage(Request $request)
	{
		return $this->renderShellPage($request, 'usuarios/form', array_merge(
			$this->service->formData(),
			array(
				'pageTitle' => __('New User'),
				'submitLabel' => __('Save'),
				'backUrl' => url('usuarios'),
				'formAction' => route('usuarios.store.page'),
				'formMethod' => 'POST',
			)
		));
	}

	public function editPage(Request $request, $id)
	{
		$payload = $this->service->editPayload((int) $id);
		if (!is_array($payload) || empty($payload['id_usu'])) {
			abort(404, __('User not found.'));
		}

		return $this->renderShellPage($request, 'usuarios/form', array_merge(
			$this->service->formData($payload),
			array(
				'pageTitle' => __('Edit User'),
				'submitLabel' => __('Save'),
				'backUrl' => url('usuarios'),
				'formAction' => route('usuarios.update.page', (int) $id),
				'formMethod' => 'PATCH',
			)
		));
	}

	public function storePage(UserStoreRequest $request)
	{
		$result = $this->service->create($request->all());
		if ($result->isDuplicate()) {
			return redirect()->back()->withInput()->withErrors(array(
				'login_usu' => __('Duplicate record.'),
			));
		}

		if (!$result->isSuccess()) {
			return redirect()->back()->withInput()->withErrors(array(
				'form' => __('Operation failed.'),
			));
		}

		return redirect()->route('usuarios')->with('status', __('User created successfully.'));
	}

	public function updatePage(UserUpdateRequest $request, $id)
	{
		$input = $request->all();
		$input['id_usu'] = (int) $id;
		$result = $this->service->update($input);
		if ($result->isDuplicate()) {
			return redirect()->back()->withInput()->withErrors(array(
				'login_usu' => __('Duplicate record.'),
			));
		}

		if (!$result->isSuccess()) {
			return redirect()->back()->withInput()->withErrors(array(
				'form' => __('Operation failed.'),
			));
		}

		return redirect()->route('usuarios')->with('status', __('User updated successfully.'));
	}

	public function destroyPage($id)
	{
		$result = $this->service->delete((int) $id);

		return redirect()->route('usuarios')->with(
			$result->isSuccess() ? 'status' : 'error',
			$result->isSuccess() ? __('User deleted successfully.') : __('Operation failed.')
		);
	}

	public function show($id)
	{
		$data = $this->service->editPayload((int) $id);
		if (!is_array($data)) {
			return $this->apiJsonResponse(false, 'not_found', __('User not found.'), array(), 404);
		}

		return $this->apiJsonResponse(true, 'loaded', __('User loaded.'), $data);
	}

	public function store(UserStoreRequest $request)
	{
		return $this->mapWriteResultToJson($this->service->create($request->all()), __('User created successfully.'));
	}

	public function update(UserUpdateRequest $request, $id)
	{
		$input = $request->all();
		$input['id_usu'] = (int) $id;

		return $this->mapWriteResultToJson($this->service->update($input), __('User updated successfully.'));
	}

	public function destroy($id)
	{
		return $this->mapWriteResultToJson($this->service->delete((int) $id), __('User deleted successfully.'));
	}

	private function renderShellPage(Request $request, $viewName, array $viewData)
	{
		$user = $this->authService->currentUser();
		if (empty($user)) {
			throw new \RuntimeException(__('User session not found.'));
		}

		$this->authService->syncSessionContext($user);
		$pageData = $this->mainPageService->build(array(
			'section' => 'usuarios',
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
