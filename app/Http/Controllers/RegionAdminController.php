<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ValidatesLegacyFormRequest;
use App\Http\Requests\RegionStoreRequest;
use App\Http\Requests\RegionUpdateRequest;
use App\Services\RegionAdminService;
use App\Support\View;
use Illuminate\Http\Request;

class RegionAdminController extends Controller
{
	use ValidatesLegacyFormRequest;

	/** @var RegionAdminService */
	private $service;

	/** @var View */
	private $view;

	public function __construct(RegionAdminService $service, View $view)
	{
		$this->service = $service;
		$this->view = $view;
	}

	public function index()
	{
		return $this->view->render('regioes/index', $this->service->indexData());
	}

	public function ajax(array $input)
	{
		$flag = isset($input['flag']) ? (string) $input['flag'] : '';
		if ($flag === 'E') {
			return $this->service->editPayload(isset($input['regiao_id']) ? (int) $input['regiao_id'] : 0);
		}
		if ($flag === 'I') {
			return $this->service->create($input);
		}
		if ($flag === 'U') {
			return $this->service->update($input);
		}
		if ($flag === 'D') {
			return $this->service->delete(isset($input['regiao_id']) ? (int) $input['regiao_id'] : 0);
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
		$jsonMode = (string) $request->input('response_format', '') === 'json';
		if ($flag === 'I' && !$this->validateLegacyFormRequest($request, RegionStoreRequest::class)) {
			return $jsonMode ? $this->apiJsonResponse(false, 'validation_error', 'Dados invalidos.') : $this->legacyTextResponse('0');
		}
		if ($flag === 'U' && !$this->validateLegacyFormRequest($request, RegionUpdateRequest::class)) {
			return $jsonMode ? $this->apiJsonResponse(false, 'validation_error', 'Dados invalidos.') : $this->legacyTextResponse('0');
		}

		if ($jsonMode) {
			return $this->webAjaxJson($request, $flag);
		}

		return $this->legacyTextResponse($this->ajax($request->all()));
	}

	private function webAjaxJson(Request $request, $flag)
	{
		$input = $request->all();

		if ($flag === 'E') {
			$payload = $this->service->editPayload(isset($input['regiao_id']) ? (int) $input['regiao_id'] : 0);
			$data = $payload !== '' ? json_decode($payload, true) : null;
			if (!is_array($data)) {
				return $this->apiJsonResponse(false, 'not_found', 'Regiao nao encontrada.');
			}

			return $this->apiJsonResponse(true, 'loaded', 'Regiao carregada.', $data);
		}

		if ($flag === 'I') {
			return $this->mapWriteResultToJson($this->service->create($input), 'Regiao criada com sucesso.');
		}

		if ($flag === 'U') {
			return $this->mapWriteResultToJson($this->service->update($input), 'Regiao atualizada com sucesso.');
		}

		if ($flag === 'D') {
			$result = $this->service->delete(isset($input['regiao_id']) ? (int) $input['regiao_id'] : 0);
			if ($result === '3') {
				return $this->apiJsonResponse(false, 'linked_users', 'Existem usuarios vinculados a esta regiao.');
			}

			return $this->mapWriteResultToJson($result, 'Regiao excluida com sucesso.');
		}

		return $this->apiJsonResponse(false, 'invalid_flag', 'Operacao invalida.');
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
