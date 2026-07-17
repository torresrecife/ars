<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\AndamentoStoreRequest;
use App\Http\Requests\AndamentoUpdateRequest;
use App\Services\AndamentoAdminService;
use App\Support\View;

class AndamentoAdminController extends Controller
{
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

	public function show($id)
	{
		$payload = json_decode($this->service->editPayload((int) $id), true);
		if (!is_array($payload) || empty($payload['anda_id'])) {
			return $this->apiJsonResponse(false, 'not_found', 'Andamento nao encontrado.', array(), 404);
		}

		return $this->apiJsonResponse(true, 'loaded', 'Andamento carregado.', $payload);
	}

	public function store(AndamentoStoreRequest $request)
	{
		return $this->mapWriteResultToJson($this->service->create($request->all()), 'Andamento criado com sucesso.');
	}

	public function update(AndamentoUpdateRequest $request, $id)
	{
		$input = $request->all();
		$input['anda_id'] = (int) $id;

		return $this->mapWriteResultToJson($this->service->update($input), 'Andamento atualizado com sucesso.');
	}

	public function destroy($id)
	{
		return $this->mapWriteResultToJson($this->service->delete((int) $id), 'Andamento excluido com sucesso.');
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
