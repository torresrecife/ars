<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\UserAdminService;
use App\Support\View;

class UserAdminController
{
	/** @var UserAdminService */
	private $service;

	/** @var View */
	private $view;

	public function __construct(UserAdminService $service, View $view)
	{
		$this->service = $service;
		$this->view = $view;
	}

	public function index()
	{
		return $this->view->render('usuarios/index', $this->service->indexData());
	}

	public function ajax(array $input)
	{
		$flag = isset($input['flag']) ? (string) $input['flag'] : '';
		if ($flag === 'E') {
			return $this->service->editPayload(isset($input['id_usu']) ? (int) $input['id_usu'] : 0);
		}
		if ($flag === 'I') {
			return $this->service->create($input);
		}
		if ($flag === 'U') {
			return $this->service->update($input);
		}
		if ($flag === 'D') {
			return $this->service->delete(isset($input['id_usu']) ? (int) $input['id_usu'] : 0);
		}

		return '0';
	}
}
