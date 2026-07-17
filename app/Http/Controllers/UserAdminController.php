<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Services\UserAdminService;
use App\Support\View;

class UserAdminController extends Controller
{
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

	public function show($id)
	{
		$payload = $this->service->editPayload((int) $id);
		$data = $payload !== '' ? json_decode($payload, true) : null;
		if (!is_array($data)) {
			return $this->apiJsonResponse(false, 'not_found', 'Usuario nao encontrado.', array(), 404);
		}

		return $this->apiJsonResponse(true, 'loaded', 'Usuario carregado.', $data);
	}

	public function store(UserStoreRequest $request)
	{
		return $this->mapWriteResultToJson($this->service->create($request->all()), 'Usuario criado com sucesso.');
	}

	public function update(UserUpdateRequest $request, $id)
	{
		$input = $request->all();
		$input['id_usu'] = (int) $id;

		return $this->mapWriteResultToJson($this->service->update($input), 'Usuario atualizado com sucesso.');
	}

	public function destroy($id)
	{
		return $this->mapWriteResultToJson($this->service->delete((int) $id), 'Usuario excluido com sucesso.');
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
