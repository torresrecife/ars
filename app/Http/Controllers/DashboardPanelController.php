<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\DashboardPanelService;

class DashboardPanelController
{
	/** @var DashboardPanelService */
	private $service;

	public function __construct(DashboardPanelService $service)
	{
		$this->service = $service;
	}

	public function index(array $input = array())
	{
		$viewData = $this->service->build($input);

		ob_start();
		include base_path('views/dashboard/panel.php');
		return (string) ob_get_clean();
	}
}
