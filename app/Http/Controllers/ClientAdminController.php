<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ClientStoreRequest;
use App\Http\Requests\ClientUpdateRequest;
use App\Services\ClientAdminService;
use App\Support\View;

class ClientAdminController extends Controller
{
	/** @var ClientAdminService */
	private $service;

	/** @var View */
	private $view;

	public function __construct(ClientAdminService $service, View $view)
	{
		$this->service = $service;
		$this->view = $view;
	}

	public function index()
	{
		return $this->view->render('clientes/index', $this->service->indexData());
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
}
