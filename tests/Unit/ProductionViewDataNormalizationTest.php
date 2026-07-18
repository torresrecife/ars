<?php

namespace Tests\Unit;

use App\ViewModels\DashboardFinancialSummary;
use App\ViewModels\DashboardMetricCell;
use App\ViewModels\DashboardMetricRow;
use App\ViewModels\DashboardPrejudiceRow;
use App\ViewModels\MonthlyProductionRow;
use App\ViewModels\MonthlyProductionTotals;
use App\ViewModels\MonthlyProductionViewData;
use App\ViewModels\PanelViewData;
use App\ViewModels\WeeklyProductionRow;
use App\ViewModels\WeeklyProductionTotals;
use App\ViewModels\WeeklyProductionViewData;
use Tests\TestCase;

class ProductionViewDataNormalizationTest extends TestCase
{
    public function test_panel_view_data_normalizes_nested_dtos()
    {
        $viewData = new PanelViewData(array(
            'productionRows' => array(
                new DashboardMetricRow(
                    7,
                    'Ajuizamento',
                    array(new DashboardMetricCell(10, 8, 80, 'circle_yellow.png', array('1', '2'))),
                    10,
                    8,
                    80,
                    'circle_yellow.png',
                    array('1', '2')
                ),
            ),
            'prejudiceRows' => array(
                new DashboardPrejudiceRow(
                    9,
                    'Custas',
                    array(new DashboardMetricCell(0, 100, 0, '', array('3'))),
                    100,
                    array('3')
                ),
            ),
            'summary' => new DashboardFinancialSummary(
                array(new DashboardMetricCell(10, 8, 80, 'circle_yellow.png')),
                10,
                8,
                80,
                'circle_yellow.png',
                7,
                70,
                'circle_red.png'
            ),
        ));

        $payload = $viewData->toArray();

        $this->assertSame('Ajuizamento', $payload['productionRows'][0]['name']);
        $this->assertSame(8.0, $payload['productionRows'][0]['weekData'][0]['real']);
        $this->assertSame(100.0, $payload['prejudiceRows'][0]['totalReal']);
        $this->assertSame(70.0, $payload['summary']['netPercent']);
    }

    public function test_weekly_and_monthly_view_data_normalize_nested_dtos()
    {
        $weekly = new WeeklyProductionViewData(array(
            'rows' => array(
                new WeeklyProductionRow('Banco X', 1000, 500, 450, -50, 90, 45, 'red', array('1')),
            ),
            'totals' => new WeeklyProductionTotals(1000, 500, 450, -50, 90, 45, 'red'),
        ));

        $monthly = new MonthlyProductionViewData(array(
            'rows' => array(
                new MonthlyProductionRow(
                    'Banco X',
                    array(new DashboardMetricCell(100, 90, 90, 'circle_yellow.png', array('1'))),
                    100,
                    90,
                    90,
                    'circle_yellow.png'
                ),
            ),
            'totals' => new MonthlyProductionTotals(
                array(new DashboardMetricCell(100, 90, 90, 'circle_yellow.png')),
                100,
                90,
                90,
                'circle_yellow.png'
            ),
        ));

        $weeklyPayload = $weekly->toArray();
        $monthlyPayload = $monthly->toArray();

        $this->assertSame('Banco X', $weeklyPayload['rows'][0]['name']);
        $this->assertSame(45.0, $weeklyPayload['totals']['percentMonth']);
        $this->assertSame(90.0, $monthlyPayload['rows'][0]['weekData'][0]['real']);
        $this->assertSame('circle_yellow.png', $monthlyPayload['totals']['weeks'][0]['icon']);
    }
}
