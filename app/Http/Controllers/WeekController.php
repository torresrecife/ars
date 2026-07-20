<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\WeekStoreRequest;
use App\Http\Requests\WeekUpdateRequest;
use App\Services\WeekService;
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
			'months' => array(
				1 => __('January'),
				2 => __('February'),
				3 => __('March'),
				4 => __('April'),
				5 => __('May'),
				6 => __('June'),
				7 => __('July'),
				8 => __('August'),
				9 => __('September'),
				10 => __('October'),
				11 => __('November'),
				12 => __('December'),
			),
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
		return $this->weekService->delete((int) $id)
			? $this->apiJsonResponse(true, 'success', __('Week deleted successfully.'))
			: $this->apiJsonResponse(false, 'error', __('Operation failed.'), array(), 500);
	}

	private function mapWriteResultToJson($result, $successMessage)
	{
		if ((string) $result === '1') {
			return $this->apiJsonResponse(true, 'success', $successMessage);
		}
		if ((string) $result === '2') {
			return $this->apiJsonResponse(false, 'duplicate', __('Duplicate record.'), array(), 409);
		}

		return $this->apiJsonResponse(false, 'error', __('Operation failed.'), array(), 500);
	}
}
