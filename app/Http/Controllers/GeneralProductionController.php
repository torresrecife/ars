<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\GeneralProductionService;
use App\Support\View;
use Illuminate\Http\Request;

class GeneralProductionController
{
	/** @var GeneralProductionService */
	private $service;

	/** @var View */
	private $view;

	public function __construct(GeneralProductionService $service, View $view)
	{
		$this->service = $service;
		$this->view = $view;
	}

	public function weekly(array $input, array $session)
	{
		return $this->view->render('geral/weekly', $this->service->buildWeekly($input, $session));
	}

	public function monthly(array $input, array $session)
	{
		return $this->view->render('geral/monthly', $this->service->buildMonthly($input, $session));
	}

	public function webWeekly(Request $request)
	{
		return response($this->weekly($request->all(), $request->session()->all()));
	}

	public function webMonthly(Request $request)
	{
		return response($this->monthly($request->all(), $request->session()->all()));
	}
}
