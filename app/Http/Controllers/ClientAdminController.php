<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ValidatesLegacyFormRequest;
use App\Http\Requests\ClientStoreRequest;
use App\Http\Requests\ClientUpdateRequest;
use App\Services\ClientAdminService;
use App\Support\View;
use Illuminate\Http\Request;

class ClientAdminController extends Controller
{
	use ValidatesLegacyFormRequest;

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

	public function ajax(array $input)
	{
		$flag = isset($input['flag']) ? (string) $input['flag'] : '';
		if ($flag === 'E') {
			return $this->service->editPayload(isset($input['banco_id']) ? (int) $input['banco_id'] : 0);
		}
		if ($flag === 'I') {
			return $this->service->create($input);
		}
		if ($flag === 'U') {
			return $this->service->update($input);
		}
		if ($flag === 'D') {
			return $this->service->delete(isset($input['banco_id']) ? (int) $input['banco_id'] : 0);
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
		if ($flag === 'I' && !$this->validateLegacyFormRequest($request, ClientStoreRequest::class)) {
			return $this->legacyTextResponse('0');
		}
		if ($flag === 'U' && !$this->validateLegacyFormRequest($request, ClientUpdateRequest::class)) {
			return $this->legacyTextResponse('0');
		}

		return $this->legacyTextResponse($this->ajax($request->all()));
	}
}
