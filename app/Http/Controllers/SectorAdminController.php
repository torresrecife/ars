<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\SectorAdminService;
use App\Support\View;
use Illuminate\Http\Request;

class SectorAdminController
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
		$headers = array(
			'Content-Type' => 'text/plain; charset=UTF-8',
		);

		if (isset($input['flag']) && (string) $input['flag'] === 'E') {
			$headers['Content-Type'] = 'text/html; charset=ISO-8859-1';
		}

		return response($this->ajax($input), 200, $headers);
	}
}
