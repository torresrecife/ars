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
				1 => 'Janeiro',
				2 => 'Fevereiro',
				3 => 'Março',
				4 => 'Abril',
				5 => 'Maio',
				6 => 'Junho',
				7 => 'Julho',
				8 => 'Agosto',
				9 => 'Setembro',
				10 => 'Outubro',
				11 => 'Novembro',
				12 => 'Dezembro',
			),
		));
	}

	public function show($id)
	{
		$row = $this->weekService->findById((int) $id);
		if (!$row) {
			return $this->apiJsonResponse(false, 'not_found', 'Semana nao encontrada.', array(), 404);
		}

		return $this->apiJsonResponse(true, 'loaded', 'Semana carregada.', $row);
	}

	public function store(WeekStoreRequest $request)
	{
		return $this->mapWriteResultToJson($this->weekService->createFromRequest($request->all()), 'Semana criada com sucesso.');
	}

	public function update(WeekUpdateRequest $request, $id)
	{
		$input = $request->all();
		$input['id_sem'] = (int) $id;

		return $this->mapWriteResultToJson($this->weekService->updateFromRequest($input), 'Semana atualizada com sucesso.');
	}

	public function destroy($id)
	{
		return $this->weekService->delete((int) $id)
			? $this->apiJsonResponse(true, 'success', 'Semana excluida com sucesso.')
			: $this->apiJsonResponse(false, 'error', 'Falha na operacao.', array(), 500);
	}

	private function mapWriteResultToJson($result, $successMessage)
	{
		if ((string) $result === '1') {
			return $this->apiJsonResponse(true, 'success', $successMessage);
		}
		if ((string) $result === '2') {
			return $this->apiJsonResponse(false, 'duplicate', 'Registro duplicado.', array(), 409);
		}

		return $this->apiJsonResponse(false, 'error', 'Falha na operacao.', array(), 500);
	}
}
