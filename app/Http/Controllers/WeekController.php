<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ValidatesLegacyFormRequest;
use App\Http\Requests\WeekStoreRequest;
use App\Http\Requests\WeekUpdateRequest;
use App\Services\WeekService;
use App\Support\View;
use Illuminate\Http\Request;

class WeekController extends Controller
{
	use ValidatesLegacyFormRequest;

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

	public function ajax(array $input = array())
	{
		$flag = isset($input['flag']) ? (string) $input['flag'] : '';

		if ($flag === 'E') {
			$row = $this->weekService->findById(isset($input['id_sem']) ? (int) $input['id_sem'] : 0);
			if (!$row) {
				return '';
			}

			return implode('-|-', array_values($row)) . '-|-';
		}

		if ($flag === 'I') {
			return (string) $this->weekService->createFromRequest($input);
		}

		if ($flag === 'U') {
			return (string) $this->weekService->updateFromRequest($input);
		}

		if ($flag === 'D') {
			return $this->weekService->delete(isset($input['id_sem']) ? (int) $input['id_sem'] : 0) ? '1' : '0';
		}

		return '0';
	}

	public function webIndex(Request $request)
	{
		return response($this->index());
	}

	public function webAjax(Request $request)
	{
		$input = $request->all();
		$flag = isset($input['flag']) ? (string) $input['flag'] : '';

		if (isset($input['flag']) && (string) $input['flag'] === 'I' && !$this->validateLegacyFormRequest($request, WeekStoreRequest::class)) {
			return $this->apiJsonResponse(false, 'validation_error', 'Dados invalidos.', array(), 422);
		}
		if (isset($input['flag']) && (string) $input['flag'] === 'U' && !$this->validateLegacyFormRequest($request, WeekUpdateRequest::class)) {
			return $this->apiJsonResponse(false, 'validation_error', 'Dados invalidos.', array(), 422);
		}

		return $this->webAjaxJson($input, $flag);
	}

	private function webAjaxJson(array $input, $flag)
	{
		if ($flag === 'E') {
			$row = $this->weekService->findById(isset($input['id_sem']) ? (int) $input['id_sem'] : 0);
			if (!$row) {
				return $this->apiJsonResponse(false, 'not_found', 'Semana nao encontrada.', array(), 404);
			}

			return $this->apiJsonResponse(true, 'loaded', 'Semana carregada.', $row);
		}

		if ($flag === 'I') {
			return $this->mapWriteResultToJson($this->weekService->createFromRequest($input), 'Semana criada com sucesso.');
		}

		if ($flag === 'U') {
			return $this->mapWriteResultToJson($this->weekService->updateFromRequest($input), 'Semana atualizada com sucesso.');
		}

		if ($flag === 'D') {
			return $this->weekService->delete(isset($input['id_sem']) ? (int) $input['id_sem'] : 0)
				? $this->apiJsonResponse(true, 'success', 'Semana excluida com sucesso.')
				: $this->apiJsonResponse(false, 'error', 'Falha na operacao.', array(), 500);
		}

		return $this->apiJsonResponse(false, 'invalid_flag', 'Operacao invalida.', array(), 400);
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
