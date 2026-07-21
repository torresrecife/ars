<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\SectorAdminService;
use App\Support\View;
use Illuminate\Http\Request;

class SectorAdminController extends Controller
{
	/** @var SectorAdminService */
	private $service;

	/** @var View */
	private $view;

	public function __construct(SectorAdminService $service, View $view)
	{
		$this->service = $service;
		$this->view = $view;
	}

	public function index()
	{
		return $this->view->render('setores/index', $this->service->indexData());
	}

	public function show($id)
	{
		$payload = $this->service->editPayload((int) $id);
		if ($payload === '') {
			return $this->apiJsonResponse(false, 'not_found', __('Sector not found.'), array(), 404);
		}

		$parts = explode('-|-', $payload);

		return $this->apiJsonResponse(true, 'loaded', __('Sector loaded.'), array(
			'area_id' => isset($parts[0]) ? (int) $parts[0] : 0,
			'area_nome' => isset($parts[1]) ? (string) $parts[1] : '',
			'area_date' => isset($parts[2]) ? (string) $parts[2] : '',
		));
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
}
