<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\RegionStoreRequest;
use App\Http\Requests\RegionUpdateRequest;
use App\Services\RegionAdminService;
use App\Support\View;

class RegionAdminController extends Controller
{
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

	public function show($id)
	{
		$payload = $this->service->editPayload((int) $id);
		$data = $payload !== '' ? json_decode($payload, true) : null;
		if (!is_array($data)) {
			return $this->apiJsonResponse(false, 'not_found', 'Regiao nao encontrada.', array(), 404);
		}

		return $this->apiJsonResponse(true, 'loaded', 'Regiao carregada.', $data);
	}

	public function store(RegionStoreRequest $request)
	{
		return $this->mapWriteResultToJson($this->service->create($request->all()), 'Regiao criada com sucesso.');
	}

	public function update(RegionUpdateRequest $request, $id)
	{
		$input = $request->all();
		$input['regiao_id'] = (int) $id;

		return $this->mapWriteResultToJson($this->service->update($input), 'Regiao atualizada com sucesso.');
	}

	public function destroy($id)
	{
		$result = $this->service->delete((int) $id);
		if ($result === '3') {
			return $this->apiJsonResponse(false, 'linked_users', 'Existem usuarios vinculados a esta regiao.', array(), 409);
		}

		return $this->mapWriteResultToJson($result, 'Regiao excluida com sucesso.');
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
