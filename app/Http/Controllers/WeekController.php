<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\WeekStoreRequest;
use App\Http\Requests\WeekUpdateRequest;
use App\Services\WeekService;
use App\Support\MonthMap;
use App\Support\View;

class WeekController extends Controller
{
	/** @var WeekService */
	private $weekService;

	/** @var View */
	private $view;

	public function __construct(WeekService $weekService, View $view)
	{
		$this->weekService = $weekService;
		$this->view = $view;
	}

	public function index()
	{
		return $this->view->render('semanas/index', array(
			'weeks' => $this->weekService->all(),
			'months' => MonthMap::localized(),
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

	public function update(WeekUpdateRequest $request, $id)
	{
		$input = $request->all();
		$input['id_sem'] = (int) $id;

		return $this->mapWriteResultToJson($this->weekService->updateFromRequest($input), __('Week updated successfully.'));
	}

	public function destroy($id)
	{
		return $this->mapWriteResultToJson($this->weekService->delete((int) $id), __('Week deleted successfully.'));
	}
}
