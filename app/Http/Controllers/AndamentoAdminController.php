<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\AndamentoAdminService;
use Illuminate\Http\Request;

class AndamentoAdminController
{
	/** @var AndamentoAdminService */
	private $service;

	public function __construct(AndamentoAdminService $service)
	{
		$this->service = $service;
	}

	public function webAjax(Request $request)
	{
		$input = $request->all();
		$flag = isset($input['flag']) ? (string) $input['flag'] : '';

		if ($flag === 'E') {
			return response($this->service->editPayload(isset($input['anda_id']) ? (int) $input['anda_id'] : 0), 200, array(
				'Content-Type' => 'application/json; charset=UTF-8',
			));
		}

		if ($flag === 'I') {
			return response($this->service->create($input), 200, array(
				'Content-Type' => 'text/plain; charset=UTF-8',
			));
		}

		if ($flag === 'U') {
			return response($this->service->update($input), 200, array(
				'Content-Type' => 'text/plain; charset=UTF-8',
			));
		}

		if ($flag === 'D') {
			return response($this->service->delete(isset($input['anda_id']) ? (int) $input['anda_id'] : 0), 200, array(
				'Content-Type' => 'text/plain; charset=UTF-8',
			));
		}

		return response('0', 200, array(
			'Content-Type' => 'text/plain; charset=UTF-8',
		));
	}
}
