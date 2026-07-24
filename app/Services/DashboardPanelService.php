<?php

declare(strict_types=1);

namespace App\Services;

use App\Domain\Metrics\PerformanceMetricFormatter;
use App\Repositories\DashboardRepository;
use App\Repositories\NeoPanelRepository;
use App\Repositories\NeoSqlsrvExecutor;
use App\Support\LegacyDate;
use App\ViewModels\DashboardFinancialSummary;
use App\ViewModels\DashboardMetricCell;
use App\ViewModels\DashboardMetricRow;
use App\ViewModels\DashboardPanelContext;
use App\ViewModels\DashboardPrejudiceRow;
use App\ViewModels\DashboardRegionFilter;
use App\ViewModels\PanelViewData;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

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

	/** @var PerformanceMetricFormatter */
	private $metricFormatter;

	public function __construct(DashboardRepository $dashboardRepository, NeoPanelRepository $neoRepository, RegionService $regionService, array $months, PerformanceMetricFormatter $metricFormatter)
	{
		$this->dashboardRepository = $dashboardRepository;
		$this->neoRepository = $neoRepository;
		$this->regionService = $regionService;
		$this->months = $months;
		$this->metricFormatter = $metricFormatter;
	}

	public function build($input, array $session = array())
	{
		$context = $this->buildContext($input, $session);
		if ($context->bankId() <= 0) {
			return new PanelViewData(array('error' => __('Go back and select the client.')));
		}

		$bank = $this->dashboardRepository->findBankById($context->bankId());
		if (!$bank) {
			return new PanelViewData(array('error' => __('Client not found.')));
		}
		if (!$this->neoRepository->isAvailable()) {
			return new PanelViewData(array('error' => __('The NEO connection is not available in this environment.')));
		}

		$cacheKey = $this->cacheKey($context);
		$ttl = max(0, (int) config('app.performance.panel_cache_ttl_seconds', 300));
		if ($ttl > 0) {
			$cached = Cache::get($cacheKey);
			if (is_array($cached)) {
				$this->logPerformance('dashboard.panel', $context, true, array());

				return new PanelViewData($cached);
			}
		}

		NeoSqlsrvExecutor::resetStats();
		$startedAt = microtime(true);
		$payload = $this->buildPayload($context, $bank);
		$elapsedMs = (microtime(true) - $startedAt) * 1000;

		if ($ttl > 0) {
			Cache::put($cacheKey, $payload, $ttl);
		}

		$this->logPerformance('dashboard.panel', $context, false, array(
			'elapsed_ms' => round($elapsedMs, 2),
		) + NeoSqlsrvExecutor::stats());

		return new PanelViewData($payload);
	}

	private function buildPayload(DashboardPanelContext $context, array $bank)
	{
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

		return array(
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
		);
	}

	private function cacheKey(DashboardPanelContext $context)
	{
		return 'dashboard_panel:' . md5(json_encode(array(
			'bank_id' => $context->bankId(),
			'area_id' => $context->areaId(),
			'month' => $context->month(),
			'year' => $context->year(),
			'user_id' => $context->userId(),
			'user_level' => $context->userLevel(),
			'user_region_mode' => $context->userRegionMode(),
			'region_id' => $context->selectedRegionId(),
		)));
	}

	private function logPerformance($channel, DashboardPanelContext $context, $cacheHit, array $metrics)
	{
		if (!config('app.performance.neo_query_log_enabled', false)) {
			return;
		}

		Log::info($channel, array(
			'cache_hit' => (bool) $cacheHit,
			'bank_id' => $context->bankId(),
			'area_id' => $context->areaId(),
			'month' => $context->month(),
			'year' => $context->year(),
			'user_id' => $context->userId(),
			'region_id' => $context->selectedRegionId(),
		) + $metrics);
	}

	private function buildProductionRows($bankId, $month, $year, array $weeks, array $carteiraCodes, $carteiraMode, array $ufCodes = array(), $regionId = 0, array $metaRegionIds = array())
	{
		$rows = $this->dashboardRepository->listMetaRowsByBankMonthYearAndSpecies($bankId, $month, $year, 1, array(), array(), $regionId, $metaRegionIds);
		$rows = $this->groupMetaRowsByAnda($rows, $weeks);
		$typeSets = $this->mapRowTypes($rows);
		$built = array();
		$weekLookups = array();

		foreach ($weeks as $index => $week) {
			$events = $this->loadProductionWeekEvents(
				$bankId,
				$this->collectUniqueTypes($typeSets),
				$carteiraCodes,
				$carteiraMode,
				$week,
				$month,
				$year,
				$ufCodes
			);
			$weekLookups[$index] = $this->buildProductionLookup($events);
		}

		foreach ($rows as $row) {
			$weekData = array();
			$totalReal = 0;
			$totalCodes = array();
			$totalMeta = (int) round(isset($row['totalMeta']) ? (float) $row['totalMeta'] : (float) $row['meta_valor']);
			$rowTypes = isset($typeSets[(int) $row['anda_id']]) ? $typeSets[(int) $row['anda_id']] : array();

			foreach ($weeks as $index => $week) {
				$metaValue = isset($row['weekMeta'][$index]) ? (float) $row['weekMeta'][$index] : $this->resolveWeekMeta($row, $index, $weeks);
				$queryResult = $this->aggregateProductionLookup($weekLookups[$index], $rowTypes);
				$realValue = count($queryResult['codes']);
				$weekData[] = new DashboardMetricCell(
					$metaValue,
					$realValue,
					$this->metricFormatter->percent($realValue, $metaValue),
					$this->metricFormatter->percentIcon($realValue, $metaValue),
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
				$this->metricFormatter->percent($totalReal, $totalMeta),
				$this->metricFormatter->percentIcon($totalReal, $totalMeta),
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
		$typeSets = $this->mapRowTypes($rows);
		$built = array();
		$weekLookups = array();

		foreach ($weeks as $index => $week) {
			$events = $this->loadFinancialWeekEvents(
				$bankId,
				$this->collectUniqueTypes($typeSets),
				$carteiraCodes,
				$carteiraMode,
				$week,
				$month,
				$year,
				$ufCodes
			);
			$weekLookups[$index] = $this->buildFinancialLookup($events);
		}

		foreach ($rows as $row) {
			$weekData = array();
			$totalReal = 0.0;
			$totalCodes = array();
			$totalMeta = isset($row['totalMeta']) ? (float) $row['totalMeta'] : (float) $row['meta_valor'];
			$rowTypes = isset($typeSets[(int) $row['anda_id']]) ? $typeSets[(int) $row['anda_id']] : array();

			foreach ($weeks as $index => $week) {
				$metaValue = isset($row['weekMeta'][$index]) ? (float) $row['weekMeta'][$index] : $this->resolveWeekMeta($row, $index, $weeks);
				$queryResult = $this->aggregateFinancialLookup($weekLookups[$index], $rowTypes);
				$realValue = (float) $queryResult['total'];
				$weekData[] = new DashboardMetricCell(
					$metaValue,
					$realValue,
					$this->metricFormatter->percent($realValue, $metaValue),
					$this->metricFormatter->percentIcon($realValue, $metaValue),
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
				$this->metricFormatter->percent($totalReal, $totalMeta),
				$this->metricFormatter->percentIcon($totalReal, $totalMeta),
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
		$typeSets = $this->mapRowTypes($rows);
		$built = array();
		$weekLookups = array();

		foreach ($weeks as $index => $week) {
			$events = $this->loadFinancialWeekEvents(
				$bankId,
				$this->collectUniqueTypes($typeSets),
				$carteiraCodes,
				$carteiraMode,
				$week,
				$month,
				$year,
				$ufCodes
			);
			$weekLookups[$index] = $this->buildFinancialLookup($events);
		}

		foreach ($rows as $row) {
			$weekData = array();
			$totalReal = 0.0;
			$totalCodes = array();
			$rowTypes = isset($typeSets[(int) $row['anda_id']]) ? $typeSets[(int) $row['anda_id']] : array();

			foreach ($weeks as $index => $week) {
				$queryResult = $this->aggregateFinancialLookup($weekLookups[$index], $rowTypes);
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
				$this->metricFormatter->percent($weekReal, $weekMeta, 1),
				$this->metricFormatter->percentIcon($weekReal, $weekMeta)
			);
			$realTotal += $weekReal;
		}

		foreach ($financialRows as $row) {
			$rowData = $row->toArray();
			$metaTotal += (float) $rowData['totalMeta'];
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
			$this->metricFormatter->percent($realTotal, $metaTotal, 1),
			$this->metricFormatter->percentIcon($realTotal, $metaTotal),
			$netRealTotal,
			$this->metricFormatter->percent($netRealTotal, $metaTotal, 1),
			$this->metricFormatter->percentIcon($netRealTotal, $metaTotal)
		);
	}

	private function buildContext($input, array $session)
	{
		$payload = is_object($input) && method_exists($input, 'toArray') ? $input->toArray() : (array) $input;

		return new DashboardPanelContext(
			isset($payload['bank_id']) ? $payload['bank_id'] : 0,
			isset($payload['area_id']) ? $payload['area_id'] : '',
			isset($payload['mes']) ? $payload['mes'] : date('m'),
			isset($payload['ano']) ? $payload['ano'] : date('Y'),
			isset($session['usuarioID']) ? $session['usuarioID'] : 0,
			isset($session['usuarioNivel']) ? $session['usuarioNivel'] : '',
			isset($session['usuarioRegiaoModo']) ? $session['usuarioRegiaoModo'] : 'N',
			isset($payload['regiao_id']) ? $payload['regiao_id'] : 0
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
						' | ' . __('Region') . ': <b>' . $region['regiao_nome'] . '</b>',
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
				$region ? ' | ' . __('Region') . ': <b>' . $region['regiao_nome'] . '</b>' : '',
				array($selectedRegionId)
			);
		}

		if ($level === 'GER' && in_array($mode, array('R', 'T'), true)) {
			if ($selectedRegionId > 0 && in_array($selectedRegionId, $regionIds, true)) {
				$region = $this->regionService->findUserRegion($context->userId(), $selectedRegionId);

				return new DashboardRegionFilter(
					$selectedRegionId,
					$this->regionService->listUfsByRegionIds(array($selectedRegionId)),
					$region ? ' | ' . __('Region') . ': <b>' . $region['regiao_nome'] . '</b>' : '',
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
					$region ? ' | ' . __('Region') . ': <b>' . $region['regiao_nome'] . '</b>' : '',
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
				'label' => __('All regions'),
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

	private function mapRowTypes(array $rows)
	{
		$mapped = array();
		foreach ($rows as $row) {
			$andaId = isset($row['anda_id']) ? (int) $row['anda_id'] : 0;
			if ($andaId <= 0) {
				continue;
			}

			$mapped[$andaId] = $this->splitNeoTypes(isset($row['anda_neo']) ? $row['anda_neo'] : '');
		}

		return $mapped;
	}

	private function collectUniqueTypes(array $typeSets)
	{
		$unique = array();
		foreach ($typeSets as $types) {
			foreach ($types as $type) {
				$unique[$type] = $type;
			}
		}

		return array_values($unique);
	}

	private function buildProductionLookup(array $events)
	{
		$lookup = array();
		foreach ($events as $event) {
			$type = isset($event['type_name']) ? $this->normalizeTypeName((string) $event['type_name']) : '';
			$code = isset($event['code']) ? (string) $event['code'] : '';
			if ($type === '' || $code === '') {
				continue;
			}

			$lookup[$type][$code] = $code;
		}

		return $lookup;
	}

	private function aggregateProductionLookup(array $lookup, array $rowTypes)
	{
		$codes = array();
		foreach ($rowTypes as $type) {
			foreach ($lookup as $lookupType => $typeCodes) {
				if (!$this->typeKeysMatch($type, $lookupType)) {
					continue;
				}

				foreach ($typeCodes as $code) {
					$codes[(string) $code] = (string) $code;
				}
			}
		}

		return array('codes' => array_values($codes));
	}

	private function buildFinancialLookup(array $events)
	{
		$lookup = array();
		foreach ($events as $event) {
			$type = isset($event['type_name']) ? $this->normalizeTypeName((string) $event['type_name']) : '';
			$code = isset($event['code']) ? (string) $event['code'] : '';
			$value = isset($event['value']) ? (float) $event['value'] : 0.0;
			if ($type === '' || $code === '') {
				continue;
			}

			$lookup[$type][] = array(
				'code' => $code,
				'value' => $value,
			);
		}

		return $lookup;
	}

	private function aggregateFinancialLookup(array $lookup, array $rowTypes)
	{
		$total = 0.0;
		$codes = array();
		$seen = array();

		foreach ($rowTypes as $type) {
			foreach ($lookup as $lookupType => $entries) {
				if (!$this->typeKeysMatch($type, $lookupType)) {
					continue;
				}

				foreach ($entries as $entry) {
					$key = (string) $entry['code'] . '|' . number_format((float) $entry['value'], 2, '.', '');
					if (isset($seen[$key])) {
						continue;
					}

					$seen[$key] = true;
					$total += (float) $entry['value'];
					$codes[(string) $entry['code']] = (string) $entry['code'];
				}
			}
		}

		return array(
			'total' => $total,
			'codes' => array_values($codes),
		);
	}

	private function loadProductionWeekEvents($bankId, array $typeNames, array $carteiraCodes, $carteiraMode, array $week, $month, $year, array $ufCodes = array())
	{
		$ttl = max(0, (int) config('app.performance.neo_result_cache_ttl_seconds', 900));
		if ($ttl <= 0) {
			return $this->neoRepository->listProductionEventsByWeek($typeNames, $carteiraCodes, $carteiraMode, $week, $month, $year, $ufCodes);
		}

		$key = $this->neoCacheKey('panel_production_week', $bankId, $typeNames, $carteiraCodes, $carteiraMode, $month, $year, $ufCodes, $week);

		return Cache::remember($key, now()->addSeconds($ttl), function () use ($typeNames, $carteiraCodes, $carteiraMode, $week, $month, $year, $ufCodes) {
			return $this->neoRepository->listProductionEventsByWeek($typeNames, $carteiraCodes, $carteiraMode, $week, $month, $year, $ufCodes);
		});
	}

	private function loadFinancialWeekEvents($bankId, array $typeNames, array $carteiraCodes, $carteiraMode, array $week, $month, $year, array $ufCodes = array())
	{
		$ttl = max(0, (int) config('app.performance.neo_result_cache_ttl_seconds', 900));
		if ($ttl <= 0) {
			return $this->neoRepository->listFinancialEventsByWeek($typeNames, $carteiraCodes, $carteiraMode, $week, $month, $year, $ufCodes);
		}

		$key = $this->neoCacheKey('panel_financial_week', $bankId, $typeNames, $carteiraCodes, $carteiraMode, $month, $year, $ufCodes, $week);

		return Cache::remember($key, now()->addSeconds($ttl), function () use ($typeNames, $carteiraCodes, $carteiraMode, $week, $month, $year, $ufCodes) {
			return $this->neoRepository->listFinancialEventsByWeek($typeNames, $carteiraCodes, $carteiraMode, $week, $month, $year, $ufCodes);
		});
	}

	private function neoCacheKey($prefix, $bankId, array $typeNames, array $carteiraCodes, $carteiraMode, $month, $year, array $ufCodes = array(), array $week = array())
	{
		sort($typeNames);
		sort($carteiraCodes);
		sort($ufCodes);

		return $prefix . ':' . md5(json_encode(array(
			'bank_id' => (int) $bankId,
			'types' => array_values($typeNames),
			'carteira_codes' => array_values($carteiraCodes),
			'carteira_mode' => (string) $carteiraMode,
			'month' => (int) $month,
			'year' => (int) $year,
			'ufs' => array_values($ufCodes),
			'week' => array(
				'start' => isset($week['start']) ? (int) $week['start'] : 0,
				'end' => isset($week['end']) ? (int) $week['end'] : 0,
			),
		)));
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

	private function normalizeTypeName($value)
	{
		$value = trim((string) $value);
		if ($value === '') {
			return '';
		}

		$ascii = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
		if (is_string($ascii) && $ascii !== '') {
			$value = $ascii;
		}

		$value = strtolower($value);
		$value = preg_replace('/[^a-z0-9]+/', '', $value);

		return trim((string) $value);
	}

	private function typeKeysMatch($left, $right)
	{
		$left = $this->normalizeTypeName($left);
		$right = $this->normalizeTypeName($right);
		if ($left === '' || $right === '') {
			return false;
		}

		return $left === $right;
	}

}
