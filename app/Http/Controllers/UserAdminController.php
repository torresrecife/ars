<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ValidatesLegacyFormRequest;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Services\UserAdminService;
use App\Support\View;
use Illuminate\Http\Request;

class UserAdminController extends Controller
{
	use ValidatesLegacyFormRequest;

	/** @var UserAdminService */
	private $service;

	/** @var View */
	private $view;

	public function __construct(UserAdminService $service, View $view)
	{
		$this->service = $service;
		$this->view = $view;
	}

	public function index()
	{
		return $this->view->render('usuarios/index', $this->service->indexData());
	}

	public function ajax(array $input)
	{
		$flag = isset($input['flag']) ? (string) $input['flag'] : '';
		if ($flag === 'E') {
			return $this->service->editPayload(isset($input['id_usu']) ? (int) $input['id_usu'] : 0);
		}
		if ($flag === 'I') {
			return $this->service->create($input);
		}
		if ($flag === 'U') {
			return $this->service->update($input);
		}
		if ($flag === 'D') {
			return $this->service->delete(isset($input['id_usu']) ? (int) $input['id_usu'] : 0);
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
		if ($flag === 'I' && !$this->validateLegacyFormRequest($request, UserStoreRequest::class)) {
			return $this->apiJsonResponse(false, 'validation_error', 'Dados invalidos.', array(), 422);
		}
		if ($flag === 'U' && !$this->validateLegacyFormRequest($request, UserUpdateRequest::class)) {
			return $this->apiJsonResponse(false, 'validation_error', 'Dados invalidos.', array(), 422);
		}

		return $this->webAjaxJson($request, $flag);
	}

	private function webAjaxJson(Request $request, $flag)
	{
		$input = $request->all();

		if ($flag === 'E') {
			$payload = $this->service->editPayload(isset($input['id_usu']) ? (int) $input['id_usu'] : 0);
			$data = $payload !== '' ? json_decode($payload, true) : null;
			if (!is_array($data)) {
				return $this->apiJsonResponse(false, 'not_found', 'Usuario nao encontrado.', array(), 404);
			}

			return $this->apiJsonResponse(true, 'loaded', 'Usuario carregado.', $data);
		}

		if ($flag === 'I') {
			return $this->mapWriteResultToJson($this->service->create($input), 'Usuario criado com sucesso.');
		}

		if ($flag === 'U') {
			return $this->mapWriteResultToJson($this->service->update($input), 'Usuario atualizado com sucesso.');
		}

		if ($flag === 'D') {
			return $this->mapWriteResultToJson($this->service->delete(isset($input['id_usu']) ? (int) $input['id_usu'] : 0), 'Usuario excluido com sucesso.');
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
