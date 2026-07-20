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
