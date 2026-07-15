<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\DashboardPanelService;
use App\Support\View;
use Illuminate\Http\Request;

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

	public function index(array $input = array(), array $session = array())
	{
		$viewData = $this->service->build($input, $session);
		return $this->view->render('dashboard/panel', array('viewData' => $viewData));
	}

	public function webIndex(Request $request)
	{
		if (session_status() !== PHP_SESSION_ACTIVE) {
			session_start();
		}

		return response($this->index($request->all(), $_SESSION));
	}
}
