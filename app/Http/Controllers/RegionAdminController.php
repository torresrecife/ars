<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\RegionStoreRequest;
use App\Http\Requests\RegionUpdateRequest;
use App\Services\RegionAdminService;
use App\Support\View;

class RegionAdminController extends Controller
{
	/** @var RegionAdminService */
	private $service;

	/** @var View */
	private $view;

	public function __construct(RegionAdminService $service, View $view)
	{
		$this->service = $service;
		$this->view = $view;
	}

	public function index()
	{
		return $this->view->render('regioes/index', $this->service->indexData());
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
		$result = $this->service->delete((int) $id);
		if ($result === '3') {
			return $this->apiJsonResponse(false, 'linked_users', __('There are users linked to this region.'), array(), 409);
		}

		return $this->mapWriteResultToJson($result, __('Region deleted successfully.'));
	}
}
