<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\DashboardRepository;
use App\Repositories\NeoPanelRepository;
use App\Support\LegacyDate;
use App\ViewModels\DashboardFinancialSummary;
use App\ViewModels\DashboardMetricCell;
use App\ViewModels\DashboardMetricRow;
use App\ViewModels\DashboardPanelContext;
use App\ViewModels\DashboardPrejudiceRow;
use App\ViewModels\DashboardRegionFilter;
use App\ViewModels\PanelViewData;

class DashboardPanelService
{
	/** @var DashboardRepository */
	private $dashboardRepository;

	/** @var NeoPanelRepository */
	private $neoRepository;

	/** @var array */
	private $months;

	/** @var RegionService */
	private $regionService;

	public function __construct(DashboardRepository $dashboardRepository, NeoPanelRepository $neoRepository, RegionService $regionService, array $months)
	{
		$this->dashboardRepository = $dashboardRepository;
		$this->neoRepository = $neoRepository;
		$this->regionService = $regionService;
		$this->months = $months;
	}

	public function build(array $input, array $session = array())
	{
		$context = $this->buildContext($input, $session);
		if ($context->bankId() <= 0) {
			return new PanelViewData(array('error' => 'Volte e selecione o Banco!'));
		}

		$bank = $this->dashboardRepository->findBankById($context->bankId());
		if (!$bank) {
			return new PanelViewData(array('error' => 'Banco nao encontrado.'));
		}
		if (!$this->neoRepository->isAvailable()) {
			return new PanelViewData(array('error' => 'A conexao com o NEO nao esta disponivel neste ambiente.'));
		}

		$weekConfig = $this->dashboardRepository->findWeekByMonthYear($context->month(), $context->year());
		$weeks = $this->resolveWeeks($context->month(), $context->year(), $weekConfig);
		$carteiraMode = $this->dashboardRepository->findCarteiraConditionByBankId($context->bankId());
		$carteiraCodes = $this->dashboardRepository->listCarteiraCodesByBankId($context->bankId());
		$visibleRegionIds = $this->resolveVisibleRegionIds($context);
		$regionFilter = $this->resolveRegionFilter($context, $visibleRegionIds);
		$regionTabs = $this->resolveRegionTabs($context, $regionFilter->selectedRegionId(), $visibleRegionIds);
		$productionRows = $this->buildProductionRows($context->bankId(), $context->month(), $context->year(), $weeks, $carteiraCodes, $carteiraMode, $regionFilter->ufs(), $regionFilter->selectedRegionId(), $regionFilter->metaRegionIds());
		$financialRows = $this->buildFinancialRows($context->bankId(), $context->month(), $context->year(), $weeks, $carteiraCodes, $carteiraMode, $regionFilter->ufs(), $regionFilter->selectedRegionId(), $regionFilter->metaRegionIds());
		$prejudiceRows = $this->buildPrejudiceRows($context->bankId(), $context->month(), $context->year(), $weeks, $carteiraCodes, $carteiraMode, $regionFilter->ufs(), $regionFilter->selectedRegionId(), $regionFilter->metaRegionIds());
		$summary = $this->buildFinancialSummary($financialRows, $prejudiceRows, $weeks);

		return new PanelViewData(array(
			'error' => '',
			'bank' => $bank,
			'bankId' => $context->bankId(),
			'areaId' => $context->areaId(),
			'month' => $context->month(),
			'year' => $context->year(),
			'showRegionTabs' => $regionTabs['show'],
			'regionTabs' => $regionTabs['tabs'],
			'regionId' => $regionFilter->selectedRegionId(),
			'regionLabel' => $regionFilter->label(),
			'startDate' => $this->months[$context->month()] . ' / ' . $context->year(),
			'weeks' => $weeks,
			'productionRows' => $productionRows,
			'financialRows' => $financialRows,
			'prejudiceRows' => $prejudiceRows,
			'summary' => $summary,
			'ncol' => (count($weeks) * 3) + 3,
			'contentHeight' => (count($productionRows) * 30) + (count($financialRows) * 30) + (count($prejudiceRows) * 30) + 260,
		));
	}

	private function buildProductionRows($bankId, $month, $year, array $weeks, array $carteiraCodes, $carteiraMode, array $ufCodes = array(), $regionId = 0, array $metaRegionIds = array())
	{
		$rows = $this->dashboardRepository->listMetaRowsByBankMonthYearAndSpecies($bankId, $month, $year, 1, array(), array(), $regionId, $metaRegionIds);
		$rows = $this->groupMetaRowsByAnda($rows, $weeks);
		$built = array();

		foreach ($rows as $row) {
			$weekData = array();
			$totalReal = 0;
			$totalCodes = array();
			$totalMeta = (int) round(isset($row['totalMeta']) ? (float) $row['totalMeta'] : (float) $row['meta_valor']);

			foreach ($weeks as $index => $week) {
				$metaValue = isset($row['weekMeta'][$index]) ? (float) $row['weekMeta'][$index] : $this->resolveWeekMeta($row, $index, $weeks);
				$queryResult = $this->neoRepository->countProductionByWeek(
					$this->splitNeoTypes(isset($row['anda_neo']) ? $row['anda_neo'] : ''),
					$carteiraCodes,
					$carteiraMode,
					$week,
					$month,
					$year,
					$ufCodes
				);
				$realValue = (int) $queryResult['count'];
				$weekData[] = new DashboardMetricCell(
					$metaValue,
					$realValue,
					$this->percent($realValue, $metaValue),
					$this->percentIcon($realValue, $metaValue),
					$queryResult['codes']
				);
				$totalReal += $realValue;
				$totalCodes = array_merge($totalCodes, $queryResult['codes']);
			}

			$built[] = new DashboardMetricRow(
				(int) $row['anda_id'],
				$row['nome'],
				$weekData,
				$totalMeta,
				$totalReal,
				$this->percent($totalReal, $totalMeta),
				$this->percentIcon($totalReal, $totalMeta),
				array_values(array_unique($totalCodes))
			);
		}

		return $built;
	}

	private function buildFinancialRows($bankId, $month, $year, array $weeks, array $carteiraCodes, $carteiraMode, array $ufCodes = array(), $regionId = 0, array $metaRegionIds = array())
	{
		$exclude = array('CUSTAS POR FALHA OPERACIONAL');
		$rows = $this->dashboardRepository->listMetaRowsByBankMonthYearAndSpecies($bankId, $month, $year, 2, $exclude, array(), $regionId, $metaRegionIds);
		$rows = $this->groupMetaRowsByAnda($rows, $weeks);
		$built = array();

		foreach ($rows as $row) {
			$weekData = array();
			$totalReal = 0.0;
			$totalCodes = array();
			$totalMeta = isset($row['totalMeta']) ? (float) $row['totalMeta'] : (float) $row['meta_valor'];

			foreach ($weeks as $index => $week) {
				$metaValue = isset($row['weekMeta'][$index]) ? (float) $row['weekMeta'][$index] : $this->resolveWeekMeta($row, $index, $weeks);
				$queryResult = $this->neoRepository->sumFinancialByWeek(
					$this->splitNeoTypes(isset($row['anda_neo']) ? $row['anda_neo'] : ''),
					$carteiraCodes,
					$carteiraMode,
					$week,
					$month,
					$year,
					$ufCodes
				);
				$realValue = (float) $queryResult['total'];
				$weekData[] = new DashboardMetricCell(
					$metaValue,
					$realValue,
					$this->percent($realValue, $metaValue),
					$this->percentIcon($realValue, $metaValue),
					$queryResult['codes']
				);
				$totalReal += $realValue;
				$totalCodes = array_merge($totalCodes, $queryResult['codes']);
			}

			$built[] = new DashboardMetricRow(
				(int) $row['anda_id'],
				$row['nome'],
				$weekData,
				$totalMeta,
				$totalReal,
				$this->percent($totalReal, $totalMeta),
				$this->percentIcon($totalReal, $totalMeta),
				array_values(array_unique($totalCodes))
			);
		}

		return $built;
	}

	private function buildPrejudiceRows($bankId, $month, $year, array $weeks, array $carteiraCodes, $carteiraMode, array $ufCodes = array(), $regionId = 0, array $metaRegionIds = array())
	{
		$include = array('CUSTAS POR FALHA OPERACIONAL');
		$rows = $this->dashboardRepository->listMetaRowsByBankMonthYearAndSpecies($bankId, $month, $year, 2, array(), $include, $regionId, $metaRegionIds);
		$rows = $this->groupMetaRowsByAnda($rows, $weeks);
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
					$year,
					$ufCodes
				);
				$realValue = (float) $queryResult['total'];
				$weekData[] = new DashboardMetricCell(
					0.0,
					$realValue,
					0.0,
					'',
					$queryResult['codes']
				);
				$totalReal += $realValue;
				$totalCodes = array_merge($totalCodes, $queryResult['codes']);
			}

			$built[] = new DashboardPrejudiceRow(
				(int) $row['anda_id'],
				$row['nome'],
				$weekData,
				$totalReal,
				array_values(array_unique($totalCodes))
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
				$rowData = $row->toArray();
				$weekMeta += (float) $rowData['weekData'][$index]['meta'];
				$weekReal += (float) $rowData['weekData'][$index]['real'];
			}
			$weekTotals[] = new DashboardMetricCell(
				$weekMeta,
				$weekReal,
				$this->percent($weekReal, $weekMeta, 1),
				$this->percentIcon($weekReal, $weekMeta)
			);
			$metaTotal += $weekMeta;
			$realTotal += $weekReal;
		}

		foreach ($prejudiceRows as $row) {
			$rowData = $row->toArray();
			$prejudiceTotal += (float) $rowData['totalReal'];
		}

		$netRealTotal = $realTotal - $prejudiceTotal;

		return new DashboardFinancialSummary(
			$weekTotals,
			$metaTotal,
			$realTotal,
			$this->percent($realTotal, $metaTotal, 1),
			$this->percentIcon($realTotal, $metaTotal),
			$netRealTotal,
			$this->percent($netRealTotal, $metaTotal, 1),
			$this->percentIcon($netRealTotal, $metaTotal)
		);
	}

	private function buildContext(array $input, array $session)
	{
		return new DashboardPanelContext(
			isset($input['bank_id']) ? $input['bank_id'] : 0,
			isset($input['area_id']) ? $input['area_id'] : '',
			isset($input['mes']) ? $input['mes'] : date('m'),
			isset($input['ano']) ? $input['ano'] : date('Y'),
			isset($session['usuarioID']) ? $session['usuarioID'] : 0,
			isset($session['usuarioNivel']) ? $session['usuarioNivel'] : '',
			isset($session['usuarioRegiaoModo']) ? $session['usuarioRegiaoModo'] : 'N',
			isset($input['regiao_id']) ? $input['regiao_id'] : 0
		);
	}

	private function resolveRegionFilter(DashboardPanelContext $context, array $visibleRegionIds = array())
	{
		$default = new DashboardRegionFilter(0, array(), '', array());

		if ($context->userId() <= 0) {
			return $default;
		}

		$userRegions = $this->regionService->listUserRegions($context->userId());
		$regionIds = array();
		foreach ($userRegions as $region) {
			$regionIds[] = (int) $region['regiao_id'];
		}
		if (!empty($visibleRegionIds)) {
			$regionIds = array_values(array_intersect($regionIds, $visibleRegionIds));
		}

		$level = $context->userLevel();
		$mode = $context->userRegionMode();
		$selectedRegionId = $context->selectedRegionId();

		if ($level === 'ADM') {
			$allRegions = $this->regionService->listActive();
			if (empty($allRegions)) {
				return $default;
			}

			$allRegionIds = array();
			foreach ($allRegions as $region) {
				$regionId = (int) $region['regiao_id'];
				if (!empty($visibleRegionIds) && !in_array($regionId, $visibleRegionIds, true)) {
					continue;
				}
				$allRegionIds[] = $regionId;
				if ($selectedRegionId > 0 && (int) $region['regiao_id'] === $selectedRegionId) {
					return new DashboardRegionFilter(
						$selectedRegionId,
						$this->regionService->listUfsByRegionIds(array($selectedRegionId)),
						' | Regi&atilde;o: <b>' . $region['regiao_nome'] . '</b>',
						array($selectedRegionId)
					);
				}
			}

			return new DashboardRegionFilter(0, array(), '', $allRegionIds);
		}

		if (empty($regionIds)) {
			return $default;
		}

		if ($level === 'USU' && $mode === 'R') {
			$selectedRegionId = !empty($regionIds) ? (int) $regionIds[0] : 0;
			if ($selectedRegionId <= 0) {
				return $default;
			}
			$region = $this->regionService->findUserRegion($context->userId(), $selectedRegionId);

			return new DashboardRegionFilter(
				$selectedRegionId,
				$this->regionService->listUfsByRegionIds(array($selectedRegionId)),
				$region ? ' | Regi&atilde;o: <b>' . $region['regiao_nome'] . '</b>' : '',
				array($selectedRegionId)
			);
		}

		if ($level === 'GER' && in_array($mode, array('R', 'T'), true)) {
			if ($selectedRegionId > 0 && in_array($selectedRegionId, $regionIds, true)) {
				$region = $this->regionService->findUserRegion($context->userId(), $selectedRegionId);

				return new DashboardRegionFilter(
					$selectedRegionId,
					$this->regionService->listUfsByRegionIds(array($selectedRegionId)),
					$region ? ' | Regi&atilde;o: <b>' . $region['regiao_nome'] . '</b>' : '',
					array($selectedRegionId)
				);
			}

			if ($mode === 'R') {
				$selectedRegionId = !empty($regionIds) ? (int) $regionIds[0] : 0;
				if ($selectedRegionId <= 0) {
					return $default;
				}
				$region = $this->regionService->findUserRegion($context->userId(), $selectedRegionId);

				return new DashboardRegionFilter(
					$selectedRegionId,
					$this->regionService->listUfsByRegionIds(array($selectedRegionId)),
					$region ? ' | Regi&atilde;o: <b>' . $region['regiao_nome'] . '</b>' : '',
					array($selectedRegionId)
				);
			}

			return new DashboardRegionFilter(0, array(), '', $regionIds);
		}

		return $default;
	}

	private function resolveRegionTabs(DashboardPanelContext $context, $selectedRegionId, array $visibleRegionIds = array())
	{
		if ($context->userId() <= 0) {
			return array(
				'show' => false,
				'tabs' => array(),
			);
		}

		if ($context->userLevel() === 'ADM') {
			$userRegions = $this->regionService->listActive();
		} elseif ($context->userLevel() === 'GER' && in_array($context->userRegionMode(), array('R', 'T'), true)) {
			$userRegions = $this->regionService->listUserRegions($context->userId());
		} else {
			return array(
				'show' => false,
				'tabs' => array(),
			);
		}

		if (empty($userRegions)) {
			return array(
				'show' => false,
				'tabs' => array(),
			);
		}

		$tabs = array();
		if ($context->userLevel() === 'ADM' || ($context->userLevel() === 'GER' && $context->userRegionMode() === 'T')) {
			$tabs[] = array(
				'id' => 0,
				'label' => 'Todas as Regi&otilde;es',
				'active' => (int) $selectedRegionId === 0,
			);
		}

		foreach ($userRegions as $region) {
			$regionId = (int) $region['regiao_id'];
			if (!empty($visibleRegionIds) && !in_array($regionId, $visibleRegionIds, true)) {
				continue;
			}
			$tabs[] = array(
				'id' => $regionId,
				'label' => (string) $region['regiao_nome'],
				'active' => $regionId === (int) $selectedRegionId,
			);
		}

		return array(
			'show' => true,
			'tabs' => $tabs,
		);
	}

	private function resolveVisibleRegionIds(DashboardPanelContext $context)
	{
		$metaRegionIds = $this->dashboardRepository->listRegionIdsWithMetaRowsByBankMonthYear($context->bankId(), $context->month(), $context->year());

		if (empty($metaRegionIds)) {
			return array();
		}

		if ($context->userLevel() === 'ADM') {
			return $metaRegionIds;
		}

		if ($context->userId() > 0 && in_array($context->userLevel(), array('GER', 'USU'), true) && in_array($context->userRegionMode(), array('R', 'T'), true)) {
			$userRegionIds = array();
			foreach ($this->regionService->listUserRegions($context->userId()) as $region) {
				$userRegionIds[] = (int) $region['regiao_id'];
			}

			return array_values(array_intersect($userRegionIds, $metaRegionIds));
		}

		return array();
	}

	private function groupMetaRowsByAnda(array $rows, array $weeks)
	{
		$grouped = array();

		foreach ($rows as $row) {
			$andaId = isset($row['anda_id']) ? (int) $row['anda_id'] : 0;
			if ($andaId <= 0) {
				continue;
			}

			if (!isset($grouped[$andaId])) {
				$row['weekMeta'] = array_fill(0, count($weeks), 0.0);
				$row['totalMeta'] = 0.0;
				$grouped[$andaId] = $row;
			}

			foreach ($weeks as $index => $week) {
				$grouped[$andaId]['weekMeta'][$index] += $this->resolveWeekMeta($row, $index, $weeks);
			}

			$grouped[$andaId]['totalMeta'] += (float) $row['meta_valor'];
		}

		return array_values($grouped);
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
				$range = LegacyDate::legacyWeekRange((int) $month, (int) $year, (int) $index);
				$start = (int) date('d', strtotime($range['start']));
				$end = (int) date('d', strtotime($range['end']));
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
