<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Data\GeneralProductionInput;
use App\Http\Requests\GeneralProductionRequest;
use App\Services\GeneralProductionService;
use App\Support\View;

class GeneralProductionController extends Controller
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

	public function weekly($input = array(), array $session = array())
	{
		return $this->view->render(
			'geral/weekly',
			$this->service->buildWeekly($this->resolveInput($input), $session)->toArray()
		);
	}

	public function monthly($input = array(), array $session = array())
	{
		return $this->view->render(
			'geral/monthly',
			$this->service->buildMonthly($this->resolveInput($input), $session)->toArray()
		);
	}

	public function webWeekly(GeneralProductionRequest $request)
	{
		return response($this->weekly(
			GeneralProductionInput::fromArray($request->all()),
			$request->session()->all()
		));
	}

	public function webMonthly(GeneralProductionRequest $request)
	{
		return response($this->monthly(
			GeneralProductionInput::fromArray($request->all()),
			$request->session()->all()
		));
	}

	private function resolveInput($input)
	{
		if ($input instanceof GeneralProductionInput) {
			return $input;
		}

		return GeneralProductionInput::fromArray(is_array($input) ? $input : array());
	}
}
