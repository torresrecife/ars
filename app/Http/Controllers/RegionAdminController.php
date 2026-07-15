<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ValidatesLegacyFormRequest;
use App\Http\Requests\RegionStoreRequest;
use App\Http\Requests\RegionUpdateRequest;
use App\Services\RegionAdminService;
use App\Support\View;
use Illuminate\Http\Request;

class RegionAdminController
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
		if ($flag === 'I' && !$this->validateLegacyFormRequest($request, RegionStoreRequest::class)) {
			return response('0', 200, array('Content-Type' => 'text/plain; charset=UTF-8'));
		}
		if ($flag === 'U' && !$this->validateLegacyFormRequest($request, RegionUpdateRequest::class)) {
			return response('0', 200, array('Content-Type' => 'text/plain; charset=UTF-8'));
		}

		return response($this->ajax($request->all()), 200, array(
			'Content-Type' => 'text/plain; charset=UTF-8',
		));
	}
}
