<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\WeekService;
use App\Support\View;

class WeekController
{
	/** @var WeekService */
	private $weekService;

	/** @var View */
	private $view;

	public function __construct(WeekService $weekService, View $view)
	{
		$this->weekService = $weekService;
		$this->view = $view;
	}

	public function index()
	{
		return $this->view->render('semanas/index', array(
			'weeks' => $this->weekService->all(),
			'months' => array(
				1 => 'Janeiro',
				2 => 'Fevereiro',
				3 => 'Março',
				4 => 'Abril',
				5 => 'Maio',
				6 => 'Junho',
				7 => 'Julho',
				8 => 'Agosto',
				9 => 'Setembro',
				10 => 'Outubro',
				11 => 'Novembro',
				12 => 'Dezembro',
			),
		));
	}

	public function ajax(array $input = array())
	{
		$flag = isset($input['flag']) ? (string) $input['flag'] : '';

		if ($flag === 'E') {
			$row = $this->weekService->findById(isset($input['id_sem']) ? (int) $input['id_sem'] : 0);
			if (!$row) {
				return '';
			}

			return implode('-|-', array_values($row)) . '-|-';
		}

		if ($flag === 'I') {
			return (string) $this->weekService->createFromRequest($input);
		}

		if ($flag === 'U') {
			return (string) $this->weekService->updateFromRequest($input);
		}

		if ($flag === 'D') {
			return $this->weekService->delete(isset($input['id_sem']) ? (int) $input['id_sem'] : 0) ? '1' : '0';
		}

		return '0';
	}
}
