<?php

namespace Tests\Unit;

use App\Domain\Metrics\PerformanceMetricFormatter;
use App\Repositories\DashboardRepository;
use App\Repositories\NeoPanelRepository;
use App\Services\DashboardPanelService;
use App\Services\RegionService;
use App\ViewModels\DashboardMetricCell;
use App\ViewModels\DashboardMetricRow;
use App\ViewModels\DashboardPanelContext;
use Mockery;
use ReflectionClass;
use Tests\TestCase;

class DashboardPanelServiceTest extends TestCase
{
    public function test_build_production_rows_batches_week_queries()
    {
        $dashboardRepository = Mockery::mock(DashboardRepository::class);
        $dashboardRepository->shouldReceive('listMetaRowsByBankMonthYearAndSpecies')
            ->once()
            ->andReturn(array(
                array(
                    'anda_id' => 1,
                    'nome' => 'Row A',
                    'anda_neo' => 'T1',
                    'meta_valor' => 10,
                    'weekMeta' => array(10.0),
                    'totalMeta' => 10.0,
                    'especie' => 1,
                ),
                array(
                    'anda_id' => 2,
                    'nome' => 'Row B',
                    'anda_neo' => 'T2',
                    'meta_valor' => 20,
                    'weekMeta' => array(20.0),
                    'totalMeta' => 20.0,
                    'especie' => 1,
                ),
            ));

        $neoRepository = Mockery::mock(NeoPanelRepository::class);
        $neoRepository->shouldReceive('listProductionEventsByWeek')
            ->once()
            ->andReturn(array(
                array('type_name' => 'T1', 'code' => '100'),
                array('type_name' => 'T2', 'code' => '200'),
            ));
        $neoRepository->shouldNotReceive('countProductionByWeek');

        $service = new DashboardPanelService(
            $dashboardRepository,
            $neoRepository,
            Mockery::mock(RegionService::class),
            array(),
            new PerformanceMetricFormatter()
        );

        $reflection = new ReflectionClass($service);
        $method = $reflection->getMethod('buildProductionRows');
        $method->setAccessible(true);

        $rows = $method->invoke($service, 3, 7, 2026, array(array('start' => 1, 'end' => 7)), array('C1'), '=', array(), 0, array());

        $this->assertCount(2, $rows);
        $payload = $rows[0]->toArray();
        $this->assertSame(1.0, $payload['totalReal']);
        $this->assertSame(array('100'), $payload['totalCodes']);
    }

    public function test_build_financial_rows_batches_week_queries()
    {
        $dashboardRepository = Mockery::mock(DashboardRepository::class);
        $dashboardRepository->shouldReceive('listMetaRowsByBankMonthYearAndSpecies')
            ->once()
            ->andReturn(array(
                array(
                    'anda_id' => 1,
                    'nome' => 'Financial A',
                    'anda_neo' => 'F1',
                    'meta_valor' => 50,
                    'weekMeta' => array(50.0),
                    'totalMeta' => 50.0,
                    'especie' => 2,
                ),
            ));

        $neoRepository = Mockery::mock(NeoPanelRepository::class);
        $neoRepository->shouldReceive('listFinancialEventsByWeek')
            ->once()
            ->andReturn(array(
                array('type_name' => 'F1', 'code' => '900', 'value' => 12.5),
                array('type_name' => 'F1', 'code' => '901', 'value' => 7.5),
            ));
        $neoRepository->shouldNotReceive('sumFinancialByWeek');

        $service = new DashboardPanelService(
            $dashboardRepository,
            $neoRepository,
            Mockery::mock(RegionService::class),
            array(),
            new PerformanceMetricFormatter()
        );

        $reflection = new ReflectionClass($service);
        $method = $reflection->getMethod('buildFinancialRows');
        $method->setAccessible(true);

        $rows = $method->invoke($service, 3, 7, 2026, array(array('start' => 1, 'end' => 7)), array('C1'), '=', array(), 0, array());

        $this->assertCount(1, $rows);
        $payload = $rows[0]->toArray();
        $this->assertSame(20.0, $payload['totalReal']);
        $this->assertSame(array('900', '901'), $payload['totalCodes']);
    }

    public function test_build_financial_rows_matches_accented_type_names_after_normalization()
    {
        $dashboardRepository = Mockery::mock(DashboardRepository::class);
        $dashboardRepository->shouldReceive('listMetaRowsByBankMonthYearAndSpecies')
            ->once()
            ->andReturn(array(
                array(
                    'anda_id' => 1,
                    'nome' => 'Sucumbencia',
                    'anda_neo' => 'SUCUMBÊNCIA',
                    'meta_valor' => 50,
                    'weekMeta' => array(50.0),
                    'totalMeta' => 50.0,
                    'especie' => 2,
                ),
            ));

        $neoRepository = Mockery::mock(NeoPanelRepository::class);
        $neoRepository->shouldReceive('listFinancialEventsByWeek')
            ->once()
            ->andReturn(array(
                array('type_name' => 'SUCUMBENCIA', 'code' => '910', 'value' => 33.0),
            ));

        $service = new DashboardPanelService(
            $dashboardRepository,
            $neoRepository,
            Mockery::mock(RegionService::class),
            array(),
            new PerformanceMetricFormatter()
        );

        $reflection = new ReflectionClass($service);
        $method = $reflection->getMethod('buildFinancialRows');
        $method->setAccessible(true);

        $rows = $method->invoke($service, 3, 7, 2026, array(array('start' => 1, 'end' => 7)), array('C1'), '=', array(), 0, array());

        $this->assertCount(1, $rows);
        $payload = $rows[0]->toArray();
        $this->assertSame(33.0, $payload['totalReal']);
        $this->assertSame(array('910'), $payload['totalCodes']);
    }

    public function test_financial_summary_total_goal_uses_row_total_meta()
    {
        $service = new DashboardPanelService(
            Mockery::mock(DashboardRepository::class),
            Mockery::mock(NeoPanelRepository::class),
            Mockery::mock(RegionService::class),
            array(),
            new PerformanceMetricFormatter()
        );

        $rows = array(
            new DashboardMetricRow(
                1,
                'Financial Row A',
                array(new DashboardMetricCell(0, 100, 0, 'circle_grey.png')),
                250.50,
                100,
                39.9,
                'circle_red.png',
                array()
            ),
            new DashboardMetricRow(
                2,
                'Financial Row B',
                array(new DashboardMetricCell(0, 50, 0, 'circle_grey.png')),
                149.50,
                50,
                33.4,
                'circle_red.png',
                array()
            ),
        );

        $reflection = new ReflectionClass($service);
        $method = $reflection->getMethod('buildFinancialSummary');
        $method->setAccessible(true);

        $summary = $method->invoke($service, $rows, array(), array(array('start' => 1, 'end' => 7)));
        $payload = $summary->toArray();

        $this->assertSame(400.0, $payload['metaTotal']);
        $this->assertSame(150.0, $payload['realTotal']);
        $this->assertSame(37.5, $payload['grandPercent']);
    }

    public function test_manager_with_linked_regions_does_not_see_all_regions_tab_when_region_mode_is_default()
    {
        $regionService = Mockery::mock(RegionService::class);
        $regionService->shouldReceive('listUserRegions')
            ->once()
            ->with(9)
            ->andReturn(array(
                array('regiao_id' => 3, 'regiao_nome' => 'Norte'),
                array('regiao_id' => 4, 'regiao_nome' => 'Sul'),
            ));

        $service = new DashboardPanelService(
            Mockery::mock(DashboardRepository::class),
            Mockery::mock(NeoPanelRepository::class),
            $regionService,
            array(),
            new PerformanceMetricFormatter()
        );

        $reflection = new ReflectionClass($service);
        $method = $reflection->getMethod('resolveRegionTabs');
        $method->setAccessible(true);

        $tabs = $method->invoke(
            $service,
            new DashboardPanelContext(1, '1', 7, 2026, 9, 'GER', 'N', 3),
            3,
            array(3, 4)
        );

        $this->assertTrue($tabs['show']);
        $this->assertCount(2, $tabs['tabs']);
        $this->assertSame(3, $tabs['tabs'][0]['id']);
        $this->assertTrue($tabs['tabs'][0]['active']);
        $this->assertSame(4, $tabs['tabs'][1]['id']);
    }

    public function test_manager_without_linked_regions_falls_back_to_active_region_tabs()
    {
        $regionService = Mockery::mock(RegionService::class);
        $regionService->shouldReceive('listUserRegions')
            ->once()
            ->with(9)
            ->andReturn(array());
        $regionService->shouldReceive('listActive')
            ->once()
            ->andReturn(array(
                array('regiao_id' => 7, 'regiao_nome' => 'Leste'),
                array('regiao_id' => 8, 'regiao_nome' => 'Oeste'),
            ));

        $service = new DashboardPanelService(
            Mockery::mock(DashboardRepository::class),
            Mockery::mock(NeoPanelRepository::class),
            $regionService,
            array(),
            new PerformanceMetricFormatter()
        );

        $reflection = new ReflectionClass($service);
        $method = $reflection->getMethod('resolveRegionTabs');
        $method->setAccessible(true);

        $tabs = $method->invoke(
            $service,
            new DashboardPanelContext(1, '1', 7, 2026, 9, 'GER', 'N', 0),
            0,
            array(7, 8)
        );

        $this->assertTrue($tabs['show']);
        $this->assertCount(3, $tabs['tabs']);
        $this->assertSame(0, $tabs['tabs'][0]['id']);
        $this->assertTrue($tabs['tabs'][0]['active']);
        $this->assertSame(7, $tabs['tabs'][1]['id']);
        $this->assertSame(8, $tabs['tabs'][2]['id']);
    }

    public function test_manager_all_regions_uses_visible_meta_region_ids_instead_of_only_linked_regions()
    {
        $regionService = Mockery::mock(RegionService::class);
        $regionService->shouldReceive('listUserRegions')
            ->once()
            ->with(9)
            ->andReturn(array(
                array('regiao_id' => 3, 'regiao_nome' => 'Norte'),
            ));
        $regionService->shouldReceive('findUserRegion')
            ->once()
            ->with(9, 3)
            ->andReturn(array('regiao_id' => 3, 'regiao_nome' => 'Norte'));
        $regionService->shouldReceive('listUfsByRegionIds')
            ->once()
            ->with(array(3))
            ->andReturn(array('AM'));

        $service = new DashboardPanelService(
            Mockery::mock(DashboardRepository::class),
            Mockery::mock(NeoPanelRepository::class),
            $regionService,
            array(),
            new PerformanceMetricFormatter()
        );

        $reflection = new ReflectionClass($service);
        $method = $reflection->getMethod('resolveRegionFilter');
        $method->setAccessible(true);

        $filter = $method->invoke(
            $service,
            new DashboardPanelContext(1, '1', 7, 2026, 9, 'GER', 'N', 0),
            array(3, 4)
        );

        $this->assertSame(3, $filter->selectedRegionId());
        $this->assertSame(array(3), $filter->metaRegionIds());
    }
}
