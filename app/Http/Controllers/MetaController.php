<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ValidatesLegacyFormRequest;
use App\Http\Requests\MetaStoreRequest;
use App\Http\Requests\MetaUpdateRequest;
use App\Services\MetaService;
use App\Support\View;
use Illuminate\Http\Request;

class MetaController
{
	use ValidatesLegacyFormRequest;

	/** @var MetaService */
	private $metaService;

	/** @var View */
	private $view;

	public function __construct(MetaService $metaService, View $view)
	{
		$this->metaService = $metaService;
		$this->view = $view;
	}

	public function index(array $input = array(), array $session = array())
	{
		$startDate = isset($input['startDate']) ? $input['startDate'] : date('M');
		$startBanco = isset($input['startBanco']) ? $input['startBanco'] : (isset($input['banco_id']) ? $input['banco_id'] : '');
		$mes = isset($input['mes']) ? $input['mes'] : (isset($input['meta_mes']) ? $input['meta_mes'] : date('m'));
		$ano = isset($input['ano']) ? $input['ano'] : (isset($input['meta_ano']) ? $input['meta_ano'] : date('Y'));

		$bank = $this->metaService->getBank($startBanco);
		$metas = $this->metaService->listByBankMonthYear($startBanco, $mes, $ano);
		$andamentos = $this->metaService->listAndamentos();
		$regionSelection = $this->metaService->regionSelectionData($session);

		return $this->view->render('metas/index', array(
			'startDate' => $startDate,
			'startBanco' => $startBanco,
			'mes' => $mes,
			'ano' => $ano,
			'bank' => $bank,
			'metas' => $metas,
			'andamentos' => $andamentos,
			'regions' => $regionSelection['regions'],
			'allowGlobalRegion' => $regionSelection['allowGlobal'],
			'totalFinanceiro' => $this->metaService->totalFinancialMeta($metas),
			'lin' => count($metas),
			'metaTipos' => array(1 => 'Produção', 2 => 'Financeira'),
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

			$ordered = array(
				isset($row['meta_id']) ? $row['meta_id'] : '',
				isset($row['banco_id']) ? $row['banco_id'] : '',
				isset($row['meta_mes']) ? $row['meta_mes'] : '',
				isset($row['meta_ano']) ? $row['meta_ano'] : '',
				isset($row['anda_id']) ? $row['anda_id'] : '',
				isset($row['meta_valor']) ? $row['meta_valor'] : '',
				isset($row['def_sem']) ? $row['def_sem'] : '',
				isset($row['sem_1']) ? $row['sem_1'] : '',
				isset($row['sem_2']) ? $row['sem_2'] : '',
				isset($row['sem_3']) ? $row['sem_3'] : '',
				isset($row['sem_4']) ? $row['sem_4'] : '',
				isset($row['sem_5']) ? $row['sem_5'] : '',
				isset($row['regiao_id']) ? $row['regiao_id'] : '',
			);

			return implode('-|-', $ordered) . '-|-';
		}

		if ($flag === 'I') {
			return $this->metaService->createManyFromRequest($input);
		}

		if ($flag === 'U') {
			return $this->metaService->updateManyFromRequest($input);
		}

		if ($flag === 'D') {
			return $this->metaService->delete(isset($input['meta_id']) ? (int) $input['meta_id'] : 0) ? '1' : '0';
		}

		return '0';
	}

	public function webIndex(Request $request)
	{
		if (session_status() !== PHP_SESSION_ACTIVE) {
			session_start();
		}

		return response($this->index($request->all(), $_SESSION));
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
		if (isset($input['flag']) && (string) $input['flag'] === 'I' && !$this->validateLegacyFormRequest($request, MetaStoreRequest::class)) {
			return response('0', 200, $headers);
		}
		if (isset($input['flag']) && (string) $input['flag'] === 'U' && !$this->validateLegacyFormRequest($request, MetaUpdateRequest::class)) {
			return response('0', 200, $headers);
		}

		return response($this->ajax($input), 200, $headers);
	}
}
