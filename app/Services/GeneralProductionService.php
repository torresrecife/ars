<?php

declare(strict_types=1);

namespace App\Services;

use App\Domain\Metrics\PerformanceMetricFormatter;
use App\Repositories\GeneralProductionNeoRepository;
use App\Repositories\GeneralProductionRepository;
use App\Repositories\NeoSqlsrvExecutor;
use App\Support\LegacyDate;
use App\ViewModels\DashboardMetricCell;
use App\ViewModels\GeneralProductionContext;
use App\ViewModels\GeneralProductionRegionFilter;
use App\ViewModels\MonthlyProductionRow;
use App\ViewModels\MonthlyProductionTotals;
use App\ViewModels\MonthlyProductionViewData;
use App\ViewModels\WeeklyProductionRow;
use App\ViewModels\WeeklyProductionTotals;
use App\ViewModels\WeeklyProductionViewData;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GeneralProductionService
{
	/** @var GeneralProductionRepository */
	private $repository;

	/** @var GeneralProductionNeoRepository */
	private $neoRepository;

	/** @var RegionService */
	private $regionService;

	/** @var PerformanceMetricFormatter */
	private $metricFormatter;

	public function __construct(GeneralProductionRepository $repository, GeneralProductionNeoRepository $neoRepository, RegionService $regionService, PerformanceMetricFormatter $metricFormatter)
	{
		$this->repository = $repository;
		$this->neoRepository = $neoRepository;
		$this->regionService = $regionService;
		$this->metricFormatter = $metricFormatter;
	}

	public function buildWeekly($input, array $session)
	{
		$context = $this->buildContext($input, $session);
		$cacheKey = $this->cacheKey('weekly', $context);
		$ttl = max(0, (int) config('app.performance.report_cache_ttl_seconds', 300));
		if ($ttl > 0) {
			$cached = Cache::get($cacheKey);
			if (is_array($cached)) {
				$this->logPerformance('general.weekly', $context, true, array());

				return new WeeklyProductionViewData($cached);
			}
		}

		NeoSqlsrvExecutor::resetStats();
		$startedAt = microtime(true);
		$payload = $this->buildWeeklyPayload($context);
		$elapsedMs = (microtime(true) - $startedAt) * 1000;
		if ($ttl > 0) {
			Cache::put($cacheKey, $payload, $ttl);
		}
		$this->logPerformance('general.weekly', $context, false, array(
			'elapsed_ms' => round($elapsedMs, 2),
		) + NeoSqlsrvExecutor::stats());

		return new WeeklyProductionViewData($payload);
	}

	private function buildWeeklyPayload(GeneralProductionContext $context)
	{
		$regionFilter = $this->resolveRegionFilter($context);
		$banks = $this->repository->listBanks($context->userSectorId(), $context->startSector(), $context->userClientIds(), false);
		$monthPadded = str_pad((string) $context->month(), 2, '0', STR_PAD_LEFT);
		$startOfMonth = '01/' . $monthPadded . '/' . $context->year();
		$usefulDaysCurrent = (float) LegacyDate::countBusinessDaysInclusive($startOfMonth, $this->currentEndDate($context->month(), $context->year()));
		$usefulDaysMonth = (float) LegacyDate::countBusinessDaysInclusive($startOfMonth, $this->lastDayDate($context->month(), $context->year()));
		$rows = array();
		$totals = array(
			'metaMonth' => 0.0,
			'metaToday' => 0.0,
			'realized' => 0.0,
		);

		foreach ($banks as $bank) {
			$metaRows = $this->repository->listFinancialMetasByBankMonthYear($bank['banco_id'], $context->month(), $context->year(), $regionFilter->selectedRegionId());
			$aggregate = $this->aggregateFinancialMetas($metaRows);
			$carteiraCodes = $this->repository->listCarteiraCodesByBankId($bank['banco_id']);
			$carteiraMode = $this->repository->findCarteiraModeByBankId($bank['banco_id']);
			$events = $this->neoRepository->listFinancialMonthEvents($aggregate['types'], $carteiraCodes, $carteiraMode, $context->month(), $context->year(), $regionFilter->ufs());
			$sum = $this->aggregateFinancialEvents($events, $aggregate['types']);
			$metaToday = ($usefulDaysMonth > 0) ? ($aggregate['metaTotal'] / $usefulDaysMonth) * $usefulDaysCurrent : 0.0;
			$percentToday = $this->metricFormatter->percent($sum['total'], $metaToday, 1);
			$percentMonth = $this->metricFormatter->percent($sum['total'], $aggregate['metaTotal'], 1);
			$rows[] = new WeeklyProductionRow(
				$bank['banco_name'],
				$aggregate['metaTotal'],
				$metaToday,
				$sum['total'],
				$sum['total'] - $metaToday,
				$percentToday,
				$percentMonth,
				$this->metricFormatter->heatColor($percentToday),
				$sum['codes'],
				$this->metricFormatter->heatClass($percentToday)
			);
			$totals['metaMonth'] += $aggregate['metaTotal'];
			$totals['metaToday'] += $metaToday;
			$totals['realized'] += $sum['total'];
		}

		$totals['balance'] = $totals['realized'] - $totals['metaToday'];
		$totals['percentToday'] = $this->metricFormatter->percent($totals['realized'], $totals['metaToday'], 1);
		$totals['percentMonth'] = $this->metricFormatter->percent($totals['realized'], $totals['metaMonth'], 1);
		$totals['color'] = $this->metricFormatter->heatColor($totals['percentToday']);
		$totals['colorClass'] = $this->metricFormatter->heatClass($totals['percentToday']);

		return array(
			'titleArea' => $this->resolveAreaTitle($context->userSectorId(), $context->startSector()),
			'regionLabel' => $regionFilter->label(),
			'startDate' => $context->startDate(),
			'startSector' => $context->startSector(),
			'regionId' => $regionFilter->selectedRegionId(),
			'month' => $context->month(),
			'year' => $context->year(),
			'rows' => $rows,
			'totals' => new WeeklyProductionTotals(
				$totals['metaMonth'],
				$totals['metaToday'],
				$totals['realized'],
				$totals['balance'],
				$totals['percentToday'],
				$totals['percentMonth'],
				$totals['color'],
				$totals['colorClass']
			),
			'contentHeight' => (count($rows) * 30) + 360,
		);
	}

	public function buildMonthly($input, array $session)
	{
		$context = $this->buildContext($input, $session);
		$cacheKey = $this->cacheKey('monthly', $context);
		$ttl = max(0, (int) config('app.performance.report_cache_ttl_seconds', 300));
		if ($ttl > 0) {
			$cached = Cache::get($cacheKey);
			if (is_array($cached)) {
				$this->logPerformance('general.monthly', $context, true, array());

				return new MonthlyProductionViewData($cached);
			}
		}

		NeoSqlsrvExecutor::resetStats();
		$startedAt = microtime(true);
		$payload = $this->buildMonthlyPayload($context);
		$elapsedMs = (microtime(true) - $startedAt) * 1000;
		if ($ttl > 0) {
			Cache::put($cacheKey, $payload, $ttl);
		}
		$this->logPerformance('general.monthly', $context, false, array(
			'elapsed_ms' => round($elapsedMs, 2),
		) + NeoSqlsrvExecutor::stats());

		return new MonthlyProductionViewData($payload);
	}

	private function buildMonthlyPayload(GeneralProductionContext $context)
	{
		$regionFilter = $this->resolveRegionFilter($context);
		$weekConfig = $this->repository->findWeekByMonthYear($context->month(), $context->year());
		$weeks = $this->resolveWeeks($context->month(), $context->year(), $weekConfig);
		$banks = $this->repository->listBanks($context->userSectorId(), $context->startSector(), $context->userClientIds(), true);
		$rows = array();
		$weekMetaTotals = array_fill(0, count($weeks), 0.0);
		$weekRealTotals = array_fill(0, count($weeks), 0.0);
		$grandMeta = 0.0;
		$grandReal = 0.0;

		foreach ($banks as $bank) {
			$metaRows = $this->repository->listFinancialMetasByBankMonthYear($bank['banco_id'], $context->month(), $context->year(), $regionFilter->selectedRegionId());
			$aggregate = $this->aggregateFinancialMetas($metaRows);
			$carteiraCodes = $this->repository->listCarteiraCodesByBankId($bank['banco_id']);
			$carteiraMode = $this->repository->findCarteiraModeByBankId($bank['banco_id']);
			$events = $this->neoRepository->listFinancialWeekMonthEvents($aggregate['types'], $carteiraCodes, $carteiraMode, $context->month(), $context->year(), $regionFilter->ufs());
			$weeksLookup = $this->buildWeekFinancialLookup($events, $weeks, $aggregate['types']);
			$weekData = array();
			$totalReal = 0.0;

			foreach ($weeks as $index => $week) {
				$meta = $this->resolveMonthlyWeekMeta($aggregate, $index, $weeks);
				$real = isset($weeksLookup[$index]) ? $weeksLookup[$index] : array('total' => 0.0, 'codes' => array());
				$percent = $this->metricFormatter->percent($real['total'], $meta, 0);
				$weekData[] = new DashboardMetricCell(
					$meta,
					$real['total'],
					$percent,
					$this->metricFormatter->percentIcon($percent),
					$real['codes']
				);
				$weekMetaTotals[$index] += $meta;
				$weekRealTotals[$index] += $real['total'];
				$totalReal += $real['total'];
			}

			$totalPercent = $this->metricFormatter->percent($totalReal, $aggregate['metaTotal'], 0);
			$rows[] = new MonthlyProductionRow(
				$bank['banco_name'] . ($bank['banco_class'] ? ' (' . $bank['banco_class'] . ')' : ''),
				$weekData,
				$aggregate['metaTotal'],
				$totalReal,
				$totalPercent,
				$this->metricFormatter->percentIcon($totalPercent)
			);
			$grandMeta += $aggregate['metaTotal'];
			$grandReal += $totalReal;
		}

		$totalWeekData = array();
		foreach ($weeks as $index => $week) {
			$percent = $this->metricFormatter->percent($weekRealTotals[$index], $weekMetaTotals[$index], 0);
			$totalWeekData[] = new DashboardMetricCell(
				$weekMetaTotals[$index],
				$weekRealTotals[$index],
				$percent,
				$this->metricFormatter->percentIcon($percent)
			);
		}

		$grandPercent = $this->metricFormatter->percent($grandReal, $grandMeta, 0);

		return array(
			'titleArea' => $this->resolveAreaTitle($context->userSectorId(), $context->startSector()),
			'regionLabel' => $regionFilter->label(),
			'startDate' => $context->startDate(),
			'startSector' => $context->startSector(),
			'regionId' => $regionFilter->selectedRegionId(),
			'month' => $context->month(),
			'year' => $context->year(),
			'weeks' => $weeks,
			'rows' => $rows,
			'totals' => new MonthlyProductionTotals(
				$totalWeekData,
				$grandMeta,
				$grandReal,
				$grandPercent,
				$this->metricFormatter->percentIcon($grandPercent)
			),
			'contentHeight' => (count($rows) * 30) + 360,
		);
	}

	private function cacheKey($mode, GeneralProductionContext $context)
	{
		return 'general_production:' . $mode . ':' . md5(json_encode(array(
			'start_date' => $context->startDate(),
			'start_sector' => $context->startSector(),
			'month' => $context->month(),
			'year' => $context->year(),
			'user_sector_id' => $context->userSectorId(),
			'user_client_ids' => $context->userClientIds(),
			'user_id' => $context->userId(),
			'user_level' => $context->userLevel(),
			'user_region_mode' => $context->userRegionMode(),
			'region_id' => $context->selectedRegionId(),
		)));
	}

	private function logPerformance($channel, GeneralProductionContext $context, $cacheHit, array $metrics)
	{
		if (!config('app.performance.neo_query_log_enabled', false)) {
			return;
		}

		Log::info($channel, array(
			'cache_hit' => (bool) $cacheHit,
			'start_sector' => $context->startSector(),
			'month' => $context->month(),
			'year' => $context->year(),
			'user_id' => $context->userId(),
			'region_id' => $context->selectedRegionId(),
		) + $metrics);
	}

	private function buildContext($input, array $session)
	{
		$payload = is_object($input) && method_exists($input, 'toArray') ? $input->toArray() : (array) $input;

		return new GeneralProductionContext(
			isset($payload['startDate']) && $payload['startDate'] !== '' ? $payload['startDate'] : date('M'),
			isset($payload['startSetor']) ? $payload['startSetor'] : '',
			isset($payload['mes']) ? $payload['mes'] : date('m'),
			isset($payload['ano']) ? $payload['ano'] : date('Y'),
			isset($session['usuarioSetor']) ? $session['usuarioSetor'] : 0,
			isset($session['usuarioCliente']) ? $session['usuarioCliente'] : '',
			isset($session['usuarioID']) ? $session['usuarioID'] : 0,
			isset($session['usuarioNivel']) ? $session['usuarioNivel'] : '',
			isset($session['usuarioRegiaoModo']) ? $session['usuarioRegiaoModo'] : 'N',
			isset($payload['regiao_id']) ? $payload['regiao_id'] : 0
		);
	}

	private function resolveAreaTitle($userSectorId, $startSector)
	{
		if ((int) $userSectorId !== 0) {
			$name = $this->repository->findAreaNameById($userSectorId);
			return ' | ' . __('Sector') . ': <b>' . $name . '</b>';
		}

		if ((string) $startSector !== '') {
			$name = $this->repository->findAreaNameById($startSector);
			return ' ' . __('Sector') . ': <b>' . $name . '</b>';
		}

		return ' ' . __('All areas');
	}

	private function resolveRegionFilter(GeneralProductionContext $context)
	{
		$default = new GeneralProductionRegionFilter(0, array(), '');

		if ($context->userId() <= 0) {
			return $default;
		}

		$userRegions = $this->regionService->listUserRegions($context->userId());
		$regionIds = array();
		foreach ($userRegions as $region) {
			$regionIds[] = (int) $region['regiao_id'];
		}

		if (empty($regionIds)) {
			return $default;
		}

		$level = $context->userLevel();
		$mode = $context->userRegionMode();
		$selectedRegionId = $context->selectedRegionId();

		if ($level === 'USU' && $mode === 'R') {
			$selectedRegionId = (int) $regionIds[0];
			$region = $this->regionService->findUserRegion($context->userId(), $selectedRegionId);

			return new GeneralProductionRegionFilter(
				$selectedRegionId,
				$this->regionService->listUfsByRegionIds(array($selectedRegionId)),
				$region ? ' | ' . __('Region') . ': <b>' . $region['regiao_nome'] . '</b>' : ''
			);
		}

		if ($level === 'GER' && in_array($mode, array('R', 'T'), true)) {
			if ($selectedRegionId > 0 && in_array($selectedRegionId, $regionIds, true)) {
				$region = $this->regionService->findUserRegion($context->userId(), $selectedRegionId);

				return new GeneralProductionRegionFilter(
					$selectedRegionId,
					$this->regionService->listUfsByRegionIds(array($selectedRegionId)),
					$region ? ' | ' . __('Region') . ': <b>' . $region['regiao_nome'] . '</b>' : ''
				);
			}

			if ($mode === 'R') {
				return new GeneralProductionRegionFilter(
					0,
					$this->regionService->listUfsByRegionIds($regionIds),
					' | ' . __('Regions') . ': <b>' . __('All linked') . '</b>'
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

	private function aggregateFinancialEvents(array $events, array $types)
	{
		$allowedTypes = array_fill_keys($types, true);
		$total = 0.0;
		$codes = array();
		$seen = array();

		foreach ($events as $event) {
			$type = isset($event['type_name']) ? trim((string) $event['type_name']) : '';
			$code = isset($event['code']) ? (string) $event['code'] : '';
			$value = isset($event['value']) ? (float) $event['value'] : 0.0;
			if ($type === '' || $code === '' || !isset($allowedTypes[$type])) {
				continue;
			}

			$key = $type . '|' . $code . '|' . number_format($value, 2, '.', '');
			if (isset($seen[$key])) {
				continue;
			}

			$seen[$key] = true;
			$total += $value;
			$codes[$code] = $code;
		}

		return array(
			'total' => $total,
			'codes' => array_values($codes),
		);
	}

	private function buildWeekFinancialLookup(array $events, array $weeks, array $types)
	{
		$lookup = array();
		foreach ($weeks as $index => $week) {
			$lookup[$index] = array(
				'total' => 0.0,
				'codes' => array(),
				'seen' => array(),
			);
		}

		$allowedTypes = array_fill_keys($types, true);
		foreach ($events as $event) {
			$type = isset($event['type_name']) ? trim((string) $event['type_name']) : '';
			$code = isset($event['code']) ? (string) $event['code'] : '';
			$value = isset($event['value']) ? (float) $event['value'] : 0.0;
			$day = isset($event['day_number']) ? (int) $event['day_number'] : 0;
			if ($type === '' || $code === '' || $day <= 0 || !isset($allowedTypes[$type])) {
				continue;
			}

			$weekIndex = $this->resolveWeekIndexByDay($weeks, $day);
			if ($weekIndex === null) {
				continue;
			}

			$key = $type . '|' . $code . '|' . number_format($value, 2, '.', '');
			if (isset($lookup[$weekIndex]['seen'][$key])) {
				continue;
			}

			$lookup[$weekIndex]['seen'][$key] = true;
			$lookup[$weekIndex]['total'] += $value;
			$lookup[$weekIndex]['codes'][$code] = $code;
		}

		foreach ($lookup as $index => $weekData) {
			$lookup[$index] = array(
				'total' => $weekData['total'],
				'codes' => array_values($weekData['codes']),
			);
		}

		return $lookup;
	}

	private function resolveWeekIndexByDay(array $weeks, $day)
	{
		foreach ($weeks as $index => $week) {
			if ($day >= (int) $week['start'] && $day <= (int) $week['end']) {
				return $index;
			}
		}

		return null;
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

}
