<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\MetaService;
use App\Support\View;

class MetaController
{
	/** @var MetaService */
	private $metaService;

	/** @var View */
	private $view;

	public function __construct(MetaService $metaService, View $view)
	{
		$this->metaService = $metaService;
		$this->view = $view;
	}

	public function index(array $input = array())
	{
		$startDate = isset($input['startDate']) ? $input['startDate'] : date('M');
		$startBanco = isset($input['startBanco']) ? $input['startBanco'] : (isset($input['banco_id']) ? $input['banco_id'] : '');
		$mes = isset($input['mes']) ? $input['mes'] : (isset($input['meta_mes']) ? $input['meta_mes'] : date('m'));
		$ano = isset($input['ano']) ? $input['ano'] : (isset($input['meta_ano']) ? $input['meta_ano'] : date('Y'));

		$bank = $this->metaService->getBank($startBanco);
		$metas = $this->metaService->listByBankMonthYear($startBanco, $mes, $ano);
		$andamentos = $this->metaService->listAndamentos();

		return $this->view->render('metas/index', array(
			'startDate' => $startDate,
			'startBanco' => $startBanco,
			'mes' => $mes,
			'ano' => $ano,
			'bank' => $bank,
			'metas' => $metas,
			'andamentos' => $andamentos,
			'totalFinanceiro' => $this->metaService->totalFinancialMeta($metas),
			'lin' => count($metas),
			'metaTipos' => array(1 => 'ProduÃ§Ã£o', 2 => 'Financeira'),
		));
	}

	public function ajax(array $input = array())
	{
		$flag = isset($input['flag']) ? (string) $input['flag'] : '';

		if ($flag === 'E') {
			$row = $this->metaService->findById(isset($input['meta_id']) ? (int) $input['meta_id'] : 0);
			if (!$row) {
				return '';
			}

			return implode('-|-', array_values($row)) . '-|-';
		}

		if ($flag === 'I') {
			return $this->metaService->createManyFromRequest($input) ? '1' : '0';
		}

		if ($flag === 'U') {
			return $this->metaService->updateManyFromRequest($input) ? '1' : '0';
		}

		if ($flag === 'D') {
			return $this->metaService->delete(isset($input['meta_id']) ? (int) $input['meta_id'] : 0) ? '1' : '0';
		}

		return '0';
	}
}
