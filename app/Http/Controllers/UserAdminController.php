<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Services\UserAdminService;
use App\Support\View;

class UserAdminController extends Controller
{
	/** @var UserAdminService */
	private $service;

	/** @var View */
	private $view;

	public function __construct(UserAdminService $service, View $view)
	{
		$this->service = $service;
		$this->view = $view;
	}

	public function index()
	{
		return $this->view->render('usuarios/index', $this->service->indexData());
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
}
