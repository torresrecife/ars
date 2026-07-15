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
		$jsonMode = (string) $request->input('response_format', '') === 'json';

		if ($flag === 'E') {
			if ($jsonMode) {
				$payload = json_decode($this->service->editPayload(isset($input['anda_id']) ? (int) $input['anda_id'] : 0), true);
				if (!is_array($payload) || empty($payload['anda_id'])) {
					return $this->apiJsonResponse(false, 'not_found', 'Andamento nao encontrado.');
				}

				return $this->apiJsonResponse(true, 'loaded', 'Andamento carregado.', $payload);
			}

			return $this->legacyJsonResponse($this->service->editPayload(isset($input['anda_id']) ? (int) $input['anda_id'] : 0));
		}

		if ($flag === 'I') {
			if (!$this->validateLegacyFormRequest($request, AndamentoStoreRequest::class)) {
				return $jsonMode ? $this->apiJsonResponse(false, 'validation_error', 'Dados invalidos.') : $this->legacyTextResponse('0');
			}
			$result = $this->service->create($input);
			return $jsonMode ? $this->mapWriteResultToJson($result, 'Andamento criado com sucesso.') : $this->legacyTextResponse($result);
		}

		if ($flag === 'U') {
			if (!$this->validateLegacyFormRequest($request, AndamentoUpdateRequest::class)) {
				return $jsonMode ? $this->apiJsonResponse(false, 'validation_error', 'Dados invalidos.') : $this->legacyTextResponse('0');
			}
			$result = $this->service->update($input);
			return $jsonMode ? $this->mapWriteResultToJson($result, 'Andamento atualizado com sucesso.') : $this->legacyTextResponse($result);
		}

		if ($flag === 'D') {
			$result = $this->service->delete(isset($input['anda_id']) ? (int) $input['anda_id'] : 0);
			return $jsonMode ? $this->mapWriteResultToJson($result, 'Andamento excluido com sucesso.') : $this->legacyTextResponse($result);
		}

		return $jsonMode ? $this->apiJsonResponse(false, 'invalid_flag', 'Operacao invalida.') : $this->legacyTextResponse('0');
	}

	private function mapWriteResultToJson($result, $successMessage)
	{
		if ((string) $result === '1') {
			return $this->apiJsonResponse(true, 'success', $successMessage);
		}
		if ((string) $result === '2') {
			return $this->apiJsonResponse(false, 'duplicate', 'Registro duplicado.');
		}

		return $this->apiJsonResponse(false, 'error', 'Falha na operacao.');
	}
}
