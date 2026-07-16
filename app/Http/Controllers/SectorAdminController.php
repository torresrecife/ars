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

	public function ajax(array $input)
	{
		$flag = isset($input['flag']) ? (string) $input['flag'] : '';
		if ($flag === 'E') {
			$areaId = isset($input['area_id']) ? (int) $input['area_id'] : (isset($input['id_setor']) ? (int) $input['id_setor'] : 0);
			return $this->service->editPayload($areaId);
		}
		if ($flag === 'I') {
			return $this->service->create($input);
		}
		if ($flag === 'U') {
			return $this->service->update($input);
		}
		if ($flag === 'D') {
			$areaId = isset($input['area_id']) ? (int) $input['area_id'] : (isset($input['id_setor']) ? (int) $input['id_setor'] : 0);
			return $this->service->delete($areaId);
		}

		return '0';
	}

	public function webIndex(Request $request)
	{
		return response($this->index());
	}

	public function webAjax(Request $request)
	{
		$input = $request->all();
		$flag = isset($input['flag']) ? (string) $input['flag'] : '';

		if ($flag === 'I') {
			$request->validate(array(
				'area_nome' => 'required|string|max:255',
			));
		}
		if ($flag === 'U') {
			$request->validate(array(
				'area_id' => 'required|integer|min:1',
				'area_nome' => 'required|string|max:255',
			));
		}

		if ($flag === 'E') {
			$areaId = isset($input['area_id']) ? (int) $input['area_id'] : (isset($input['id_setor']) ? (int) $input['id_setor'] : 0);
			$payload = $this->service->editPayload($areaId);
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

		if ($flag === 'I') {
			return $this->mapWriteResultToJson($this->service->create($input), 'Setor criado com sucesso.');
		}

		if ($flag === 'U') {
			return $this->mapWriteResultToJson($this->service->update($input), 'Setor atualizado com sucesso.');
		}

		if ($flag === 'D') {
			$areaId = isset($input['area_id']) ? (int) $input['area_id'] : (isset($input['id_setor']) ? (int) $input['id_setor'] : 0);
			return $this->mapWriteResultToJson($this->service->delete($areaId), 'Setor excluido com sucesso.');
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
