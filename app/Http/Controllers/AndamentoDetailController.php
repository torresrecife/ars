<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Data\NeoDetailInput;
use App\Http\Requests\NeoDetailRequest;
use App\Services\NeoDetailService;
use App\Support\View;

class AndamentoDetailController extends Controller
{
	/** @var NeoDetailService */
	private $service;

	/** @var View */
	private $view;

	public function __construct(NeoDetailService $service, View $view)
	{
		$this->service = $service;
		$this->view = $view;
	}

	public function index($input = array(), array $session = array())
	{
		$viewData = $this->service->andamentoDetailViewData($this->resolveInput($input), $session);

		return $this->view->render('dados_anda/index', $viewData->toArray());
	}

	public function webIndex(NeoDetailRequest $request)
	{
		return response($this->index(
			NeoDetailInput::fromArray($request->all()),
			$request->session()->all()
		));
	}

	private function resolveInput($input)
	{
		if ($input instanceof NeoDetailInput) {
			return $input;
		}

		return NeoDetailInput::fromArray(is_array($input) ? $input : array());
	}
}
