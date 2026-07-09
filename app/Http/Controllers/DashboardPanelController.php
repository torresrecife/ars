<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\DashboardPanelService;
use Illuminate\Http\Request;

class DashboardPanelController
{
	/** @var DashboardPanelService */
	private $service;

	public function __construct(DashboardPanelService $service)
	{
		$this->service = $service;
	}

	public function index(array $input = array(), array $session = array())
	{
		$viewData = $this->service->build($input, $session);

		ob_start();
		include base_path('views/dashboard/panel.php');
		return (string) ob_get_clean();
	}

	public function webIndex(Request $request)
	{
		if (session_status() !== PHP_SESSION_ACTIVE) {
			session_start();
		}

		return response($this->index($request->all(), $_SESSION));
	}
}
