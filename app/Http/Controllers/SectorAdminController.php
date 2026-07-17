<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\SectorAdminService;
use App\Support\View;
use Illuminate\Http\Request;

class SectorAdminController extends Controller
{
	/** @var SectorAdminService */
	private $service;

	/** @var View */
	private $view;

	public function __construct(SectorAdminService $service, View $view)
	{
		$this->service = $service;
		$this->view = $view;
	}

	public function index()
	{
		return $this->view->render('setores/index', $this->service->indexData());
	}

	public function show($id)
	{
		$payload = $this->service->editPayload((int) $id);
		if ($payload === '') {
			return $this->apiJsonResponse(false, 'not_found', 'Setor nao encontrado.', array(), 404);
		}

		$parts = explode('-|-', $payload);

		return $this->apiJsonResponse(true, 'loaded', 'Setor carregado.', array(
			'area_id' => isset($parts[0]) ? (int) $parts[0] : 0,
			'area_nome' => isset($parts[1]) ? (string) $parts[1] : '',
			'area_date' => isset($parts[2]) ? (string) $parts[2] : '',
		));
	}

	public function store(Request $request)
	{
		$request->validate(array(
			'area_nome' => 'required|string|max:255',
		));

		return $this->mapWriteResultToJson($this->service->create($request->all()), 'Setor criado com sucesso.');
	}

	public function update(Request $request, $id)
	{
		$request->validate(array(
			'area_nome' => 'required|string|max:255',
		));

		$input = $request->all();
		$input['area_id'] = (int) $id;

		return $this->mapWriteResultToJson($this->service->update($input), 'Setor atualizado com sucesso.');
	}

	public function destroy($id)
	{
		return $this->mapWriteResultToJson($this->service->delete((int) $id), 'Setor excluido com sucesso.');
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
