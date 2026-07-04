<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\DashboardRepository;
use App\Repositories\NeoDetailRepository;

class NeoDetailService
{
	/** @var NeoDetailRepository */
	private $repository;

	/** @var DashboardRepository */
	private $dashboardRepository;

	/** @var RegionService */
	private $regionService;

	public function __construct(NeoDetailRepository $repository, DashboardRepository $dashboardRepository, RegionService $regionService)
	{
		$this->repository = $repository;
		$this->dashboardRepository = $dashboardRepository;
		$this->regionService = $regionService;
	}

	public function financialDetailViewData(array $input, array $session = array())
	{
		$rows = $this->resolveFinancialRows($input, $session);
		$total = 0.0;
		$count = 0;

		foreach ($rows as &$row) {
			$row['comarca_exibicao'] = $this->formatComarca(isset($row['comarca']) ? $row['comarca'] : '');
			$row['estado_exibicao'] = isset($row['estado']) ? trim((string) $row['estado']) : '';
			$row['processo_exibicao'] = $this->formatProcesso(isset($row['Processo']) ? $row['Processo'] : '');
			$row['processo_cnj_exibicao'] = $this->formatProcesso(isset($row['ProcessoCNJ']) ? $row['ProcessoCNJ'] : '');
			$total += isset($row['valores']) ? (float) $row['valores'] : 0.0;
			$count++;
		}
		unset($row);

		return array(
			'rows' => $rows,
			'bankName' => isset($input['banco_lnc']) ? (string) $input['banco_lnc'] : '',
			'totalCount' => $count,
			'totalValue' => $total,
		);
	}

	public function andamentoDetailViewData(array $input, array $session = array())
	{
		$rows = $this->resolveAndamentoRows($input, $session);
		$count = 0;

		foreach ($rows as &$row) {
			$row['comarca_exibicao'] = $this->formatComarca(isset($row['comarca']) ? $row['comarca'] : '');
			$row['estado_exibicao'] = isset($row['estado']) ? trim((string) $row['estado']) : '';
			$count++;
		}
		unset($row);

		return array(
			'rows' => $rows,
			'bankName' => isset($input['banco_and']) ? (string) $input['banco_and'] : '',
			'totalCount' => $count,
		);
	}

	private function resolveFinancialRows(array $input, array $session = array())
	{
		if ($this->hasContextRequest($input)) {
			$context = $this->buildContext($input, $session);
			if ($context !== null) {
				return $this->repository->financialDetailsByContext(
					$context['typeNames'],
					$context['carteiraCodes'],
					$context['carteiraMode'],
					$context['month'],
					$context['year'],
					$context['week'],
					$context['ufCodes']
				);
			}
		}

		$codes = $this->parseCodes(isset($input['codig_lnc']) ? $input['codig_lnc'] : '');
		return $this->repository->financialDetails($codes);
	}

	private function resolveAndamentoRows(array $input, array $session = array())
	{
		if ($this->hasContextRequest($input)) {
			$context = $this->buildContext($input, $session);
			if ($context !== null) {
				return $this->repository->andamentoDetailsByContext(
					$context['typeNames'],
					$context['carteiraCodes'],
					$context['carteiraMode'],
					$context['month'],
					$context['year'],
					$context['week'],
					$context['ufCodes']
				);
			}
		}

		$codes = $this->parseCodes(isset($input['codig_and']) ? $input['codig_and'] : '');
		return $this->repository->andamentoDetails($codes);
	}

	private function hasContextRequest(array $input)
	{
		return isset($input['detail_bank_id'], $input['detail_anda_id'], $input['detail_month'], $input['detail_year']);
	}

	private function buildContext(array $input, array $session = array())
	{
		$bankId = isset($input['detail_bank_id']) ? (int) $input['detail_bank_id'] : 0;
		$andaId = isset($input['detail_anda_id']) ? (int) $input['detail_anda_id'] : 0;
		$month = isset($input['detail_month']) ? (int) $input['detail_month'] : 0;
		$year = isset($input['detail_year']) ? (int) $input['detail_year'] : 0;
		$weekKey = isset($input['detail_week']) ? (string) $input['detail_week'] : 'total';

		if ($bankId <= 0 || $andaId <= 0 || $month <= 0 || $year <= 0) {
			return null;
		}

		$metaRow = $this->dashboardRepository->findMetaRowByBankMonthYearAndAndaId($bankId, $month, $year, $andaId);
		if (!$metaRow) {
			return null;
		}

		$carteiraCodes = $this->dashboardRepository->listCarteiraCodesByBankId($bankId);
		$carteiraMode = $this->dashboardRepository->findCarteiraConditionByBankId($bankId);
		$week = $this->resolveWeek($month, $year, $weekKey);

		return array(
			'typeNames' => $this->splitNeoTypes(isset($metaRow['anda_neo']) ? $metaRow['anda_neo'] : ''),
			'carteiraCodes' => $carteiraCodes,
			'carteiraMode' => $carteiraMode,
			'month' => $month,
			'year' => $year,
			'week' => $week,
			'ufCodes' => $this->resolveRegionFilter($input, $session),
		);
	}

	private function resolveRegionFilter(array $input, array $session)
	{
		$userId = isset($session['usuarioID']) ? (int) $session['usuarioID'] : 0;
		if ($userId <= 0) {
			return array();
		}

		$userRegions = $this->regionService->listUserRegions($userId);
		$regionIds = array();
		foreach ($userRegions as $region) {
			$regionIds[] = (int) $region['regiao_id'];
		}
		if (empty($regionIds)) {
			return array();
		}

		$level = isset($session['usuarioNivel']) ? (string) $session['usuarioNivel'] : '';
		$mode = isset($session['usuarioRegiaoModo']) ? (string) $session['usuarioRegiaoModo'] : 'N';
		$selectedRegionId = isset($input['detail_region_id']) ? (int) $input['detail_region_id'] : 0;

		if ($level === 'USU' && $mode === 'R') {
			return $this->regionService->listUfsByRegionIds(array((int) $regionIds[0]));
		}

		if ($level === 'GER' && in_array($mode, array('R', 'T'), true)) {
			if ($selectedRegionId > 0 && in_array($selectedRegionId, $regionIds, true)) {
				return $this->regionService->listUfsByRegionIds(array($selectedRegionId));
			}

			if ($mode === 'R') {
				return $this->regionService->listUfsByRegionIds($regionIds);
			}
		}

		return array();
	}

	private function resolveWeek($month, $year, $weekKey)
	{
		if ($weekKey === 'total') {
			return array();
		}

		$weekIndex = (int) $weekKey;
		if ($weekIndex < 0) {
			return array();
		}

		$weekConfig = $this->dashboardRepository->findWeekByMonthYear($month, $year);
		$weeks = $this->resolveWeeks($month, $year, $weekConfig);

		return isset($weeks[$weekIndex]) ? $weeks[$weekIndex] : array();
	}

	private function resolveWeeks($month, $year, $weekConfig)
	{
		$weeks = array();
		$validConfig = $this->isValidWeekConfig($weekConfig, $month, $year);
		$count = ($validConfig && isset($weekConfig['ini_5']) && (int) $weekConfig['ini_5'] > 0 && (int) $weekConfig['fim_5'] > 0) ? 5 : 4;

		for ($index = 1; $index <= $count; $index++) {
			if ($validConfig) {
				$start = (int) $weekConfig['ini_' . $index];
				$end = (int) $weekConfig['fim_' . $index];
			} else {
				$start = (int) date('d', strtotime(P_semana($month, $year, $index, 'ini')));
				$end = (int) date('d', strtotime(P_semana($month, $year, $index, 'fim')));
			}

			$weeks[] = array(
				'start' => $start,
				'end' => $end,
			);
		}

		return $weeks;
	}

	private function isValidWeekConfig($weekConfig, $month, $year)
	{
		if (!$weekConfig) {
			return false;
		}

		$lastDay = (int) date('t', strtotime($year . '-' . str_pad((string) $month, 2, '0', STR_PAD_LEFT) . '-01'));
		$previousEnd = 0;

		for ($index = 1; $index <= 5; $index++) {
			$start = isset($weekConfig['ini_' . $index]) ? (int) $weekConfig['ini_' . $index] : 0;
			$end = isset($weekConfig['fim_' . $index]) ? (int) $weekConfig['fim_' . $index] : 0;

			if ($start === 0 && $end === 0) {
				continue;
			}

			if ($start <= 0 || $end <= 0 || $start > $end || $end > $lastDay) {
				return false;
			}

			if ($start <= $previousEnd) {
				return false;
			}

			$previousEnd = $end;
		}

		return true;
	}

	private function parseCodes($value)
	{
		$codes = array();
		foreach (explode(',', (string) $value) as $code) {
			$code = trim($code);
			if ($code !== '' && ctype_digit($code)) {
				$codes[] = (int) $code;
			}
		}

		return $codes;
	}

	private function splitNeoTypes($value)
	{
		$items = array();
		foreach (explode(',', (string) $value) as $item) {
			$item = trim($item);
			if ($item !== '') {
				$items[] = $item;
			}
		}

		return $items;
	}

	private function formatComarca($value)
	{
		$value = trim((string) $value);
		if ($value === '') {
			return '';
		}

		return htmlentities($value, ENT_QUOTES, 'UTF-8');
	}

	private function formatProcesso($value)
	{
		$value = trim((string) $value);
		if ($value === '') {
			return '-';
		}

		if (function_exists('formatarProcesso')) {
			return formatarProcesso($value);
		}

		return $value;
	}
}
