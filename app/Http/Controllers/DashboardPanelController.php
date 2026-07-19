<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Data\DashboardPanelInput;
use App\Http\Requests\DashboardPanelRequest;
use App\Services\DashboardPanelService;
use App\Support\View;

class DashboardPanelController extends Controller
{
	/** @var DashboardPanelService */
	private $service;

	/** @var View */
	private $view;

	public function __construct(DashboardPanelService $service, View $view)
	{
		$this->service = $service;
		$this->view = $view;
	}

	public function index($input = array(), array $session = array())
	{
		$viewData = $this->service->build($this->resolveInput($input), $session);

		return $this->view->render('dashboard/panel', array('viewData' => $viewData->toArray()));
	}

	public function webIndex(DashboardPanelRequest $request)
	{
		return response($this->index(
			DashboardPanelInput::fromArray($request->all()),
			$request->session()->all()
		));
	}

	private function resolveInput($input)
	{
		if ($input instanceof DashboardPanelInput) {
			return $input;
		}

		return DashboardPanelInput::fromArray(is_array($input) ? $input : array());
	}
}
