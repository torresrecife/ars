<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ClientStoreRequest;
use App\Http\Requests\ClientUpdateRequest;
use App\Services\ClientAdminService;
use App\Support\View;

class ClientAdminController extends Controller
{
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

	public function show($id)
	{
		$payload = $this->service->editPayload((int) $id);
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

	public function store(ClientStoreRequest $request)
	{
		return $this->mapWriteResultToJson($this->service->create($request->all()), 'Cliente criado com sucesso.');
	}

	public function update(ClientUpdateRequest $request, $id)
	{
		$input = $request->all();
		$input['banco_id'] = (int) $id;

		return $this->mapWriteResultToJson($this->service->update($input), 'Cliente atualizado com sucesso.');
	}

	public function destroy($id)
	{
		return $this->mapWriteResultToJson($this->service->delete((int) $id), 'Cliente excluido com sucesso.');
	}
}
