<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ValidatesLegacyFormRequest;
use App\Http\Requests\ClientStoreRequest;
use App\Http\Requests\ClientUpdateRequest;
use App\Services\ClientAdminService;
use App\Support\View;
use Illuminate\Http\Request;

class ClientAdminController extends Controller
{
	use ValidatesLegacyFormRequest;

	/** @var ClientAdminService */
	private $service;

	/** @var View */
	private $view;

	public function __construct(ClientAdminService $service, View $view)
	{
		$this->service = $service;
		$this->view = $view;
	}

	public function index()
	{
		return $this->view->render('clientes/index', $this->service->indexData());
	}

	public function ajax(array $input)
	{
		$flag = isset($input['flag']) ? (string) $input['flag'] : '';
		if ($flag === 'E') {
			return $this->service->editPayload(isset($input['banco_id']) ? (int) $input['banco_id'] : 0);
		}
		if ($flag === 'I') {
			return $this->service->create($input);
		}
		if ($flag === 'U') {
			return $this->service->update($input);
		}
		if ($flag === 'D') {
			return $this->service->delete(isset($input['banco_id']) ? (int) $input['banco_id'] : 0);
		}

		return '0';
	}

	public function webIndex(Request $request)
	{
		return response($this->index());
	}

	public function webAjax(Request $request)
	{
		$flag = (string) $request->input('flag', '');
		if ($flag === 'I' && !$this->validateLegacyFormRequest($request, ClientStoreRequest::class)) {
			return $this->apiJsonResponse(false, 'validation_error', 'Dados invalidos.', array(), 422);
		}
		if ($flag === 'U' && !$this->validateLegacyFormRequest($request, ClientUpdateRequest::class)) {
			return $this->apiJsonResponse(false, 'validation_error', 'Dados invalidos.', array(), 422);
		}

		return $this->webAjaxJson($request, $flag);
	}

	private function webAjaxJson(Request $request, $flag)
	{
		$input = $request->all();

		if ($flag === 'E') {
			$payload = $this->service->editPayload(isset($input['banco_id']) ? (int) $input['banco_id'] : 0);
			if ($payload === '') {
				return $this->apiJsonResponse(false, 'not_found', 'Cliente nao encontrado.', array(), 404);
			}

			$parts = explode('-|-', $payload);
			$data = array(
				'banco_id' => isset($parts[0]) ? (int) $parts[0] : 0,
				'banco_name' => isset($parts[1]) ? (string) $parts[1] : '',
				'banco_cod' => isset($parts[2]) ? (string) $parts[2] : '',
				'banco_creator' => isset($parts[3]) ? (string) $parts[3] : '',
				'banco_area' => isset($parts[4]) ? (int) $parts[4] : 0,
				'banco_status' => isset($parts[5]) ? (string) $parts[5] : '',
				'banco_class' => isset($parts[6]) ? (string) $parts[6] : '',
				'simulador' => isset($parts[7]) ? (string) $parts[7] : '',
				'banco_curto' => isset($parts[8]) ? (string) $parts[8] : '',
				'dados_codes' => (!empty($parts[9])) ? array_values(array_filter(explode('|||', $parts[9]), function ($value) {
					return $value !== '';
				})) : array(),
			);

			return $this->apiJsonResponse(true, 'loaded', 'Cliente carregado.', $data);
		}

		if ($flag === 'I') {
			return $this->mapWriteResultToJson($this->service->create($input), 'Cliente criado com sucesso.');
		}

		if ($flag === 'U') {
			return $this->mapWriteResultToJson($this->service->update($input), 'Cliente atualizado com sucesso.');
		}

		if ($flag === 'D') {
			return $this->mapWriteResultToJson($this->service->delete(isset($input['banco_id']) ? (int) $input['banco_id'] : 0), 'Cliente excluido com sucesso.');
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
