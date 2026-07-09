<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\NeoDetailService;
use App\Support\View;
use Illuminate\Http\Request;

class AndamentoDetailController
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

	public function index(array $input = array(), array $session = array())
	{
		return $this->view->render('dados_anda/index', $this->service->andamentoDetailViewData($input, $session));
	}

	public function webIndex(Request $request)
	{
		if (session_status() !== PHP_SESSION_ACTIVE) {
			session_start();
		}

		return response($this->index($request->all(), $_SESSION));
	}
}
