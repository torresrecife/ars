<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ValidatesLegacyFormRequest;
use App\Http\Requests\AndamentoStoreRequest;
use App\Http\Requests\AndamentoUpdateRequest;
use App\Services\AndamentoAdminService;
use App\Support\View;
use Illuminate\Http\Request;

class AndamentoAdminController extends Controller
{
	use ValidatesLegacyFormRequest;

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

	public function webIndex(Request $request)
	{
		return response($this->index());
	}

	public function webAjax(Request $request)
	{
		$input = $request->all();
		$flag = isset($input['flag']) ? (string) $input['flag'] : '';

		if ($flag === 'E') {
			return $this->legacyJsonResponse($this->service->editPayload(isset($input['anda_id']) ? (int) $input['anda_id'] : 0));
		}

		if ($flag === 'I') {
			if (!$this->validateLegacyFormRequest($request, AndamentoStoreRequest::class)) {
				return $this->legacyTextResponse('0');
			}
			return $this->legacyTextResponse($this->service->create($input));
		}

		if ($flag === 'U') {
			if (!$this->validateLegacyFormRequest($request, AndamentoUpdateRequest::class)) {
				return $this->legacyTextResponse('0');
			}
			return $this->legacyTextResponse($this->service->update($input));
		}

		if ($flag === 'D') {
			return $this->legacyTextResponse($this->service->delete(isset($input['anda_id']) ? (int) $input['anda_id'] : 0));
		}

		return $this->legacyTextResponse('0');
	}
}
