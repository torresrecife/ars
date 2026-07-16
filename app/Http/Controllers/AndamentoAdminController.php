<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ValidatesLegacyFormRequest;
use App\Http\Requests\AndamentoStoreRequest;
use App\Http\Requests\AndamentoUpdateRequest;
use App\Services\AndamentoAdminService;
use App\Support\View;
use Illuminate\Http\Request;

class AndamentoAdminController extends Controller
{
	use ValidatesLegacyFormRequest;

	/** @var AndamentoAdminService */
	private $service;

	/** @var View */
	private $view;

	public function __construct(AndamentoAdminService $service, View $view)
	{
		$this->service = $service;
		$this->view = $view;
	}

	public function index()
	{
		return $this->view->render('andamentos/index', $this->service->indexData());
	}

	public function webIndex(Request $request)
	{
		return response($this->index());
	}

	public function webAjax(Request $request)
	{
		$input = $request->all();
		$flag = isset($input['flag']) ? (string) $input['flag'] : '';

		if ($flag === 'E') {
			$payload = json_decode($this->service->editPayload(isset($input['anda_id']) ? (int) $input['anda_id'] : 0), true);
			if (!is_array($payload) || empty($payload['anda_id'])) {
				return $this->apiJsonResponse(false, 'not_found', 'Andamento nao encontrado.', array(), 404);
			}

			return $this->apiJsonResponse(true, 'loaded', 'Andamento carregado.', $payload);
		}

		if ($flag === 'I') {
			if (!$this->validateLegacyFormRequest($request, AndamentoStoreRequest::class)) {
				return $this->apiJsonResponse(false, 'validation_error', 'Dados invalidos.', array(), 422);
			}
			$result = $this->service->create($input);
			return $this->mapWriteResultToJson($result, 'Andamento criado com sucesso.');
		}

		if ($flag === 'U') {
			if (!$this->validateLegacyFormRequest($request, AndamentoUpdateRequest::class)) {
				return $this->apiJsonResponse(false, 'validation_error', 'Dados invalidos.', array(), 422);
			}
			$result = $this->service->update($input);
			return $this->mapWriteResultToJson($result, 'Andamento atualizado com sucesso.');
		}

		if ($flag === 'D') {
			$result = $this->service->delete(isset($input['anda_id']) ? (int) $input['anda_id'] : 0);
			return $this->mapWriteResultToJson($result, 'Andamento excluido com sucesso.');
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
