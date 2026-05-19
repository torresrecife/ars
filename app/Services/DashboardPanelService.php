<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\DashboardRepository;
use App\Repositories\NeoPanelRepository;

class DashboardPanelService
{
	/** @var DashboardRepository */
	private $dashboardRepository;

	/** @var NeoPanelRepository */
	private $neoRepository;

	/** @var array */
	private $months;

	public function __construct(DashboardRepository $dashboardRepository, NeoPanelRepository $neoRepository, array $months)
	{
		$this->dashboardRepository = $dashboardRepository;
		$this->neoRepository = $neoRepository;
		$this->months = $months;
	}

	public function build(array $input)
	{
		$bankId = isset($input['hid_flag']) ? (int) $input['hid_flag'] : 0;
		if ($bankId <= 0) {
			return array('error' => 'Volte e selecione o Banco!');
		}

		$month = isset($input['mes']) ? (int) $input['mes'] : (int) date('m');
		$year = isset($input['ano']) ? (int) $input['ano'] : (int) date('Y');
		$bank = $this->dashboardRepository->findBankById($bankId);
		if (!$bank) {
			return array('error' => 'Banco nao encontrado.');
		}
		if (!$this->neoRepository->isAvailable()) {
			return array('error' => 'A conexao com o NEO nao esta disponivel neste ambiente.');
		}

		$weekConfig = $this->dashboardRepository->findWeekByMonthYear($month, $year);
		$weeks = $this->resolveWeeks($month, $year, $weekConfig);
		$carteiraMode = $this->dashboardRepository->findCarteiraConditionByBankId($bankId);
		$carteiraCodes = $this->dashboardRepository->listCarteiraCodesByBankId($bankId);
		$productionRows = $this->buildProductionRows($bankId, $month, $year, $weeks, $carteiraCodes, $carteiraMode);
		$financialRows = $this->buildFinancialRows($bankId, $month, $year, $weeks, $carteiraCodes, $carteiraMode);
		$prejudiceRows = $this->buildPrejudiceRows($bankId, $month, $year, $weeks, $carteiraCodes, $carteiraMode);
		$summary = $this->buildFinancialSummary($financialRows, $prejudiceRows, $weeks);

		return array(
			'error' => '',
			'bank' => $bank,
			'month' => $month,
			'year' => $year,
			'startDate' => $this->months[$month] . ' / ' . $year,
			'weeks' => $weeks,
			'productionRows' => $productionRows,
			'financialRows' => $financialRows,
			'prejudiceRows' => $prejudiceRows,
			'summary' => $summary,
			'ncol' => (count($weeks) * 3) + 3,
			'contentHeight' => (count($productionRows) * 30) + (count($financialRows) * 30) + (count($prejudiceRows) * 30) + 260,
		);
	}

	private function buildProductionRows($bankId, $month, $year, array $weeks, array $carteiraCodes, $carteiraMode)
	{
		$rows = $this->dashboardRepository->listMetaRowsByBankMonthYearAndSpecies($bankId, $month, $year, 1);
		$built = array();

		foreach ($rows as $row) {
			$weekData = array();
			$totalReal = 0;
			$totalCodes = array();
			$totalMeta = (int) round((float) $row['meta_valor']);

			foreach ($weeks as $index => $week) {
				$metaValue = $this->resolveWeekMeta($row, $index, $weeks);
				$queryResult = $this->neoRepository->countProductionByWeek(
					$this->splitNeoTypes(isset($row['anda_neo']) ? $row['anda_neo'] : ''),
					$carteiraCodes,
					$carteiraMode,
					$week,
					$month,
					$year
				);
				$realValue = (int) $queryResult['count'];
				$weekData[] = array(
					'meta' => $metaValue,
					'real' => $realValue,
					'percent' => $this->percent($realValue, $metaValue),
					'icon' => $this->percentIcon($realValue, $metaValue),
					'codes' => $queryResult['codes'],
				);
				$totalReal += $realValue;
				$totalCodes = array_merge($totalCodes, $queryResult['codes']);
			}

			$built[] = array(
				'name' => $row['nome'],
				'weekData' => $weekData,
				'totalMeta' => $totalMeta,
				'totalReal' => $totalReal,
				'totalPercent' => $this->percent($totalReal, $totalMeta),
				'totalIcon' => $this->percentIcon($totalReal, $totalMeta),
				'totalCodes' => array_values(array_unique($totalCodes)),
			);
		}

		return $built;
	}

	private function buildFinancialRows($bankId, $month, $year, array $weeks, array $carteiraCodes, $carteiraMode)
	{
		$exclude = array('CUSTAS POR FALHA OPERACIONAL');
		$rows = $this->dashboardRepository->listMetaRowsByBankMonthYearAndSpecies($bankId, $month, $year, 2, $exclude);
		$built = array();

		foreach ($rows as $row) {
			$weekData = array();
			$totalReal = 0.0;
			$totalCodes = array();
			$totalMeta = (float) $row['meta_valor'];

			foreach ($weeks as $index => $week) {
				$metaValue = $this->resolveWeekMeta($row, $index, $weeks);
				$queryResult = $this->neoRepository->sumFinancialByWeek(
					$this->splitNeoTypes(isset($row['anda_neo']) ? $row['anda_neo'] : ''),
					$carteiraCodes,
					$carteiraMode,
					$week,
					$month,
					$year
				);
				$realValue = (float) $queryResult['total'];
				$weekData[] = array(
					'meta' => $metaValue,
					'real' => $realValue,
					'percent' => $this->percent($realValue, $metaValue),
					'icon' => $this->percentIcon($realValue, $metaValue),
					'codes' => $queryResult['codes'],
				);
				$totalReal += $realValue;
				$totalCodes = array_merge($totalCodes, $queryResult['codes']);
			}

			$built[] = array(
				'name' => $row['nome'],
				'weekData' => $weekData,
				'totalMeta' => $totalMeta,
				'totalReal' => $totalReal,
				'totalPercent' => $this->percent($totalReal, $totalMeta),
				'totalIcon' => $this->percentIcon($totalReal, $totalMeta),
				'totalCodes' => array_values(array_unique($totalCodes)),
			);
		}

		return $built;
	}

	private function buildPrejudiceRows($bankId, $month, $year, array $weeks, array $carteiraCodes, $carteiraMode)
	{
		$include = array('CUSTAS POR FALHA OPERACIONAL');
		$rows = $this->dashboardRepository->listMetaRowsByBankMonthYearAndSpecies($bankId, $month, $year, 2, array(), $include);
		$built = array();

		foreach ($rows as $row) {
			$weekData = array();
			$totalReal = 0.0;
			$totalCodes = array();

			foreach ($weeks as $week) {
				$queryResult = $this->neoRepository->sumFinancialByWeek(
					$this->splitNeoTypes(isset($row['anda_neo']) ? $row['anda_neo'] : ''),
					$carteiraCodes,
					$carteiraMode,
					$week,
					$month,
					$year
				);
				$realValue = (float) $queryResult['total'];
				$weekData[] = array(
					'meta' => 0.0,
					'real' => $realValue,
					'codes' => $queryResult['codes'],
				);
				$totalReal += $realValue;
				$totalCodes = array_merge($totalCodes, $queryResult['codes']);
			}

			$built[] = array(
				'name' => $row['nome'],
				'weekData' => $weekData,
				'totalReal' => $totalReal,
				'totalCodes' => array_values(array_unique($totalCodes)),
			);
		}

		return $built;
	}

	private function buildFinancialSummary(array $financialRows, array $prejudiceRows, array $weeks)
	{
		$weekTotals = array();
		$metaTotal = 0.0;
		$realTotal = 0.0;
		$prejudiceTotal = 0.0;

		foreach ($weeks as $index => $week) {
			$weekMeta = 0.0;
			$weekReal = 0.0;
			foreach ($financialRows as $row) {
				$weekMeta += (float) $row['weekData'][$index]['meta'];
				$weekReal += (float) $row['weekData'][$index]['real'];
			}
			$weekTotals[] = array(
				'meta' => $weekMeta,
				'real' => $weekReal,
				'percent' => $this->percent($weekReal, $weekMeta, 1),
				'icon' => $this->percentIcon($weekReal, $weekMeta),
			);
			$metaTotal += $weekMeta;
			$realTotal += $weekReal;
		}

		foreach ($prejudiceRows as $row) {
			$prejudiceTotal += (float) $row['totalReal'];
		}

		$netRealTotal = $realTotal - $prejudiceTotal;

		return array(
			'weekTotals' => $weekTotals,
			'metaTotal' => $metaTotal,
			'realTotal' => $realTotal,
			'grandPercent' => $this->percent($realTotal, $metaTotal, 1),
			'grandIcon' => $this->percentIcon($realTotal, $metaTotal),
			'netRealTotal' => $netRealTotal,
			'netPercent' => $this->percent($netRealTotal, $metaTotal, 1),
			'netIcon' => $this->percentIcon($netRealTotal, $metaTotal),
		);
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

	private function resolveWeekMeta(array $row, $weekIndex, array $weeks)
	{
		$weekNumber = $weekIndex + 1;
		if (isset($row['def_sem']) && $row['def_sem'] === 'Y') {
			return (float) $row['sem_' . $weekNumber];
		}

		$totalMeta = (float) $row['meta_valor'];
		$totalDays = 0;
		foreach ($weeks as $week) {
			$totalDays += $this->businessDaysForWeek($week);
		}

		$currentDays = $this->businessDaysForWeek($weeks[$weekIndex]);
		$metaValue = ($totalDays > 0) ? ($currentDays * ($totalMeta / $totalDays)) : 0;

		if ($weekIndex === count($weeks) - 1) {
			$sumPrevious = 0.0;
			for ($i = 0; $i < $weekIndex; $i++) {
				$sumPrevious += $this->resolveWeekMetaPortion($row, $weeks[$i], $totalDays);
			}

			return $totalMeta - $sumPrevious;
		}

		return $this->roundMeta($metaValue, $row['especie']);
	}

	private function resolveWeekMetaPortion(array $row, array $week, $totalDays)
	{
		$totalMeta = (float) $row['meta_valor'];
		$currentDays = $this->businessDaysForWeek($week);
		$metaValue = ($totalDays > 0) ? ($currentDays * ($totalMeta / $totalDays)) : 0;

		return $this->roundMeta($metaValue, $row['especie']);
	}

	private function businessDaysForWeek(array $week)
	{
		$distance = (int) $week['end'] - (int) $week['start'];
		$adjustment = ($distance == 8 ? 3 : ($distance == 7 ? 2 : ($distance == 6 ? 1 : 0)));

		return $distance - $adjustment;
	}

	private function roundMeta($value, $species)
	{
		if ((int) $species === 1) {
			return (float) number_format($value, 0, '.', '');
		}

		return (float) number_format($value, 2, '.', '');
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

	private function percent($real, $meta, $precision = 0)
	{
		if ((float) $meta == 0.0) {
			return 0.0;
		}

		return (float) number_format(((float) $real / (float) $meta) * 100, $precision, '.', '');
	}

	private function percentIcon($real, $meta)
	{
		if ((float) $meta == 0.0) {
			return 'circle_grey.png';
		}

		$percent = ((float) $real / (float) $meta) * 100;
		if ($percent >= 100 && $percent < 110) {
			return 'circle_green.png';
		}
		if ($percent < 100 && $percent >= 80) {
			return 'circle_yellow.png';
		}
		if ($percent >= 110) {
			return 'circle_blue.png';
		}

		return 'circle_red.png';
	}
}
