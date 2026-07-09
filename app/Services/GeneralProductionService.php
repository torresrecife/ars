<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\GeneralProductionNeoRepository;
use App\Repositories\GeneralProductionRepository;

class GeneralProductionService
{
	/** @var GeneralProductionRepository */
	private $repository;

	/** @var GeneralProductionNeoRepository */
	private $neoRepository;

	/** @var RegionService */
	private $regionService;

	public function __construct(GeneralProductionRepository $repository, GeneralProductionNeoRepository $neoRepository, RegionService $regionService)
	{
		$this->repository = $repository;
		$this->neoRepository = $neoRepository;
		$this->regionService = $regionService;
	}

	public function buildWeekly(array $input, array $session)
	{
		$context = $this->buildContext($input, $session);
		$regionFilter = $this->resolveRegionFilter($context);
		$banks = $this->repository->listBanks($context['userSectorId'], $context['startSector'], $context['userClientIds'], false);
		$monthPadded = str_pad((string) $context['month'], 2, '0', STR_PAD_LEFT);
		$startOfMonth = '01/' . $monthPadded . '/' . $context['year'];
		$usefulDaysCurrent = (float) diasUteis($startOfMonth, $this->currentEndDate($context['month'], $context['year']));
		$usefulDaysMonth = (float) diasUteis($startOfMonth, $this->lastDayDate($context['month'], $context['year']));
		$rows = array();
		$totals = array(
			'metaMonth' => 0.0,
			'metaToday' => 0.0,
			'realized' => 0.0,
		);

		foreach ($banks as $bank) {
			$metaRows = $this->repository->listFinancialMetasByBankMonthYear($bank['banco_id'], $context['month'], $context['year'], $regionFilter['selectedRegionId']);
			$aggregate = $this->aggregateFinancialMetas($metaRows);
			$carteiraCodes = $this->repository->listCarteiraCodesByBankId($bank['banco_id']);
			$carteiraMode = $this->repository->findCarteiraModeByBankId($bank['banco_id']);
			$sum = $this->neoRepository->sumFinancialByMonth($aggregate['types'], $carteiraCodes, $carteiraMode, $context['month'], $context['year'], $regionFilter['ufs']);
			$metaToday = ($usefulDaysMonth > 0) ? ($aggregate['metaTotal'] / $usefulDaysMonth) * $usefulDaysCurrent : 0.0;
			$rows[] = array(
				'name' => $bank['banco_name'],
				'metaMonth' => $aggregate['metaTotal'],
				'metaToday' => $metaToday,
				'realized' => $sum['total'],
				'balance' => $sum['total'] - $metaToday,
				'percentToday' => $this->percent($sum['total'], $metaToday, 1),
				'percentMonth' => $this->percent($sum['total'], $aggregate['metaTotal'], 1),
				'color' => $this->heatColor($this->percent($sum['total'], $metaToday, 1)),
				'codes' => $sum['codes'],
			);
			$totals['metaMonth'] += $aggregate['metaTotal'];
			$totals['metaToday'] += $metaToday;
			$totals['realized'] += $sum['total'];
		}

		$totals['balance'] = $totals['realized'] - $totals['metaToday'];
		$totals['percentToday'] = $this->percent($totals['realized'], $totals['metaToday'], 1);
		$totals['percentMonth'] = $this->percent($totals['realized'], $totals['metaMonth'], 1);
		$totals['color'] = $this->heatColor($totals['percentToday']);

		return array(
			'titleArea' => $this->resolveAreaTitle($context['userSectorId'], $context['startSector']),
			'regionLabel' => $regionFilter['label'],
			'startDate' => $context['startDate'],
			'startSector' => $context['startSector'],
			'regionId' => $regionFilter['selectedRegionId'],
			'month' => $context['month'],
			'year' => $context['year'],
			'rows' => $rows,
			'totals' => $totals,
			'contentHeight' => (count($rows) * 30) + 360,
		);
	}

	public function buildMonthly(array $input, array $session)
	{
		$context = $this->buildContext($input, $session);
		$regionFilter = $this->resolveRegionFilter($context);
		$weekConfig = $this->repository->findWeekByMonthYear($context['month'], $context['year']);
		$weeks = $this->resolveWeeks($context['month'], $context['year'], $weekConfig);
		$banks = $this->repository->listBanks($context['userSectorId'], $context['startSector'], $context['userClientIds'], true);
		$rows = array();
		$weekMetaTotals = array_fill(0, count($weeks), 0.0);
		$weekRealTotals = array_fill(0, count($weeks), 0.0);
		$grandMeta = 0.0;
		$grandReal = 0.0;

		foreach ($banks as $bank) {
			$metaRows = $this->repository->listFinancialMetasByBankMonthYear($bank['banco_id'], $context['month'], $context['year'], $regionFilter['selectedRegionId']);
			$aggregate = $this->aggregateFinancialMetas($metaRows);
			$carteiraCodes = $this->repository->listCarteiraCodesByBankId($bank['banco_id']);
			$carteiraMode = $this->repository->findCarteiraModeByBankId($bank['banco_id']);
			$weekData = array();
			$totalReal = 0.0;

			foreach ($weeks as $index => $week) {
				$meta = $this->resolveMonthlyWeekMeta($aggregate, $index, $weeks);
				$real = $this->neoRepository->sumFinancialByWeek($aggregate['types'], $carteiraCodes, $carteiraMode, $week, $context['month'], $context['year'], $regionFilter['ufs']);
				$percent = $this->percent($real['total'], $meta, 0);
				$weekData[] = array(
					'meta' => $meta,
					'real' => $real['total'],
					'percent' => $percent,
					'icon' => $this->percentIcon($percent, $meta),
					'codes' => $real['codes'],
				);
				$weekMetaTotals[$index] += $meta;
				$weekRealTotals[$index] += $real['total'];
				$totalReal += $real['total'];
			}

			$totalPercent = $this->percent($totalReal, $aggregate['metaTotal'], 0);
			$rows[] = array(
				'name' => $bank['banco_name'] . ($bank['banco_class'] ? ' (' . $bank['banco_class'] . ')' : ''),
				'weekData' => $weekData,
				'totalMeta' => $aggregate['metaTotal'],
				'totalReal' => $totalReal,
				'totalPercent' => $totalPercent,
				'totalIcon' => $this->percentIcon($totalPercent, $aggregate['metaTotal']),
			);
			$grandMeta += $aggregate['metaTotal'];
			$grandReal += $totalReal;
		}

		$totalWeekData = array();
		foreach ($weeks as $index => $week) {
			$percent = $this->percent($weekRealTotals[$index], $weekMetaTotals[$index], 0);
			$totalWeekData[] = array(
				'meta' => $weekMetaTotals[$index],
				'real' => $weekRealTotals[$index],
				'percent' => $percent,
				'icon' => $this->percentIcon($percent, $weekMetaTotals[$index]),
			);
		}

		$grandPercent = $this->percent($grandReal, $grandMeta, 0);

		return array(
			'titleArea' => $this->resolveAreaTitle($context['userSectorId'], $context['startSector']),
			'regionLabel' => $regionFilter['label'],
			'startDate' => $context['startDate'],
			'startSector' => $context['startSector'],
			'regionId' => $regionFilter['selectedRegionId'],
			'month' => $context['month'],
			'year' => $context['year'],
			'weeks' => $weeks,
			'rows' => $rows,
			'totals' => array(
				'weeks' => $totalWeekData,
				'meta' => $grandMeta,
				'real' => $grandReal,
				'percent' => $grandPercent,
				'icon' => $this->percentIcon($grandPercent, $grandMeta),
			),
			'contentHeight' => (count($rows) * 30) + 360,
		);
	}

	private function buildContext(array $input, array $session)
	{
		return array(
			'startDate' => isset($input['startDate']) && $input['startDate'] !== '' ? (string) $input['startDate'] : date('M'),
			'startSector' => isset($input['startSetor']) ? (string) $input['startSetor'] : '',
			'month' => isset($input['mes']) ? (int) $input['mes'] : (int) date('m'),
			'year' => isset($input['ano']) ? (int) $input['ano'] : (int) date('Y'),
			'userSectorId' => isset($session['usuarioSetor']) ? (int) $session['usuarioSetor'] : 0,
			'userClientIds' => isset($session['usuarioCliente']) ? (string) $session['usuarioCliente'] : '',
			'userId' => isset($session['usuarioID']) ? (int) $session['usuarioID'] : 0,
			'userLevel' => isset($session['usuarioNivel']) ? (string) $session['usuarioNivel'] : '',
			'userRegionMode' => isset($session['usuarioRegiaoModo']) ? (string) $session['usuarioRegiaoModo'] : 'N',
			'selectedRegionId' => isset($input['regiao_id']) ? (int) $input['regiao_id'] : 0,
		);
	}

	private function resolveAreaTitle($userSectorId, $startSector)
	{
		if ((int) $userSectorId !== 0) {
			$name = $this->repository->findAreaNameById($userSectorId);
			return ' | Setor: <b>' . $name . '</b>';
		}

		if ((string) $startSector !== '') {
			$name = $this->repository->findAreaNameById($startSector);
			return ' Setor: <b>' . $name . '</b>';
		}

		return ' Todas as Áreas';
	}

	private function resolveRegionFilter(array $context)
	{
		$default = array(
			'selectedRegionId' => 0,
			'ufs' => array(),
			'label' => '',
		);

		$userId = (int) $context['userId'];
		if ($userId <= 0) {
			return $default;
		}

		$userRegions = $this->regionService->listUserRegions($userId);
		$regionIds = array();
		foreach ($userRegions as $region) {
			$regionIds[] = (int) $region['regiao_id'];
		}

		if (empty($regionIds)) {
			return $default;
		}

		$level = (string) $context['userLevel'];
		$mode = (string) $context['userRegionMode'];
		$selectedRegionId = (int) $context['selectedRegionId'];

		if ($level === 'USU' && $mode === 'R') {
			$selectedRegionId = (int) $regionIds[0];
			$region = $this->regionService->findUserRegion($userId, $selectedRegionId);

			return array(
				'selectedRegionId' => $selectedRegionId,
				'ufs' => $this->regionService->listUfsByRegionIds(array($selectedRegionId)),
				'label' => $region ? ' | Regi&atilde;o: <b>' . $region['regiao_nome'] . '</b>' : '',
			);
		}

		if ($level === 'GER' && in_array($mode, array('R', 'T'), true)) {
			if ($selectedRegionId > 0 && in_array($selectedRegionId, $regionIds, true)) {
				$region = $this->regionService->findUserRegion($userId, $selectedRegionId);

				return array(
					'selectedRegionId' => $selectedRegionId,
					'ufs' => $this->regionService->listUfsByRegionIds(array($selectedRegionId)),
					'label' => $region ? ' | Regi&atilde;o: <b>' . $region['regiao_nome'] . '</b>' : '',
				);
			}

			if ($mode === 'R') {
				return array(
					'selectedRegionId' => 0,
					'ufs' => $this->regionService->listUfsByRegionIds($regionIds),
					'label' => ' | Regi&otilde;es: <b>Todas as vinculadas</b>',
				);
			}
		}

		return $default;
	}

	private function aggregateFinancialMetas(array $metaRows)
	{
		$types = array();
		$metaTotal = 0.0;
		$defSem = 'N';
		$semanas = array(0.0, 0.0, 0.0, 0.0, 0.0);

		foreach ($metaRows as $index => $row) {
			$metaTotal += (float) $row['meta_valor'];
			if ($index === 0 && isset($row['def_sem'])) {
				$defSem = (string) $row['def_sem'];
			}
			for ($week = 1; $week <= 5; $week++) {
				$semanas[$week - 1] += isset($row['sem_' . $week]) ? (float) $row['sem_' . $week] : 0.0;
			}
			foreach (explode(',', (string) $row['anda_neo']) as $type) {
				$type = trim($type);
				if ($type !== '') {
					$types[$type] = $type;
				}
			}
		}

		return array(
			'metaTotal' => $metaTotal,
			'defSem' => $defSem,
			'weeks' => $semanas,
			'types' => array_values($types),
		);
	}

	private function currentEndDate($month, $year)
	{
		if (((int) $year . str_pad((string) $month, 2, '0', STR_PAD_LEFT)) < date('Ym')) {
			return $this->lastDayDate($month, $year);
		}

		return date('d/m/Y');
	}

	private function lastDayDate($month, $year)
	{
		$lastDay = (int) date('t', strtotime($year . '-' . str_pad((string) $month, 2, '0', STR_PAD_LEFT) . '-01'));
		return str_pad((string) $lastDay, 2, '0', STR_PAD_LEFT) . '/' . str_pad((string) $month, 2, '0', STR_PAD_LEFT) . '/' . $year;
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
				'label' => $start . ' a ' . $end,
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

			if ($start <= 0 || $end <= 0 || $start > $end || $end > $lastDay || $start <= $previousEnd) {
				return false;
			}

			$previousEnd = $end;
		}

		return true;
	}

	private function resolveMonthlyWeekMeta(array $aggregate, $index, array $weeks)
	{
		if ($aggregate['defSem'] === 'Y') {
			return isset($aggregate['weeks'][$index]) ? (float) $aggregate['weeks'][$index] : 0.0;
		}

		$totalDays = 0;
		foreach ($weeks as $week) {
			$totalDays += $this->businessDaysForWeek($week);
		}

		$currentDays = $this->businessDaysForWeek($weeks[$index]);
		$meta = ($totalDays > 0) ? ($currentDays * ($aggregate['metaTotal'] / $totalDays)) : 0.0;

		if ($index === count($weeks) - 1) {
			$previous = 0.0;
			for ($i = 0; $i < $index; $i++) {
				$days = $this->businessDaysForWeek($weeks[$i]);
				$previous += number_format(($totalDays > 0) ? ($days * ($aggregate['metaTotal'] / $totalDays)) : 0.0, 2, '.', '');
			}

			return (float) number_format($aggregate['metaTotal'] - $previous, 2, '.', '');
		}

		return (float) number_format($meta, 2, '.', '');
	}

	private function businessDaysForWeek(array $week)
	{
		$distance = (int) $week['end'] - (int) $week['start'];
		$adjustment = ($distance == 8 ? 3 : ($distance == 7 ? 2 : ($distance == 6 ? 1 : 0)));

		return $distance - $adjustment;
	}

	private function percent($real, $meta, $precision)
	{
		if ((float) $meta == 0.0) {
			return 0.0;
		}

		return (float) number_format((((float) $real / (float) $meta) * 100), (int) $precision, '.', '');
	}

	private function heatColor($percent)
	{
		if ((float) $percent == 0.0) {
			return '#F0F0F0';
		}
		if ((float) $percent < 80) {
			return 'red';
		}
		if ((float) $percent < 100) {
			return '#FFB90F';
		}
		if ((float) $percent < 110) {
			return 'green';
		}

		return '#1C86EE';
	}

	private function percentIcon($percent, $meta)
	{
		if ((float) $meta == 0.0 || (float) $percent == 0.0) {
			return 'circle_grey.png';
		}
		if ((float) $percent >= 100 && (float) $percent < 110) {
			return 'circle_green.png';
		}
		if ((float) $percent < 100 && (float) $percent >= 80) {
			return 'circle_yellow.png';
		}
		if ((float) $percent >= 110) {
			return 'circle_blue.png';
		}

		return 'circle_red.png';
	}
}
