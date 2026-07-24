<?php

namespace Tests\Unit;

use App\Domain\Metrics\PerformanceMetricFormatter;
use App\Repositories\DashboardRepository;
use App\Repositories\NeoPanelRepository;
use App\Services\DashboardPanelService;
use App\Services\RegionService;
use App\ViewModels\DashboardMetricCell;
use App\ViewModels\DashboardMetricRow;
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
}
