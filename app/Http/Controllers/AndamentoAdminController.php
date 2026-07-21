<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\AndamentoStoreRequest;
use App\Http\Requests\AndamentoUpdateRequest;
use App\Services\AndamentoAdminService;
use App\Support\View;

class AndamentoAdminController extends Controller
{
	/** @var AndamentoAdminService */
	private $service;

	/** @var View */
	private $view;

	public function __construct(AndamentoAdminService $service, View $view)
	{
		$this->service = $service;
		$this->view = $view;
	}

	public function index()
	{
		return $this->view->render('andamentos/index', $this->service->indexData());
	}

	public function show($id)
	{
		$payload = json_decode($this->service->editPayload((int) $id), true);
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
}
