<?php

namespace Tests\Unit;

use App\Domain\Metrics\PerformanceMetricFormatter;
use App\Repositories\GeneralProductionNeoRepository;
use App\Repositories\GeneralProductionRepository;
use App\Services\GeneralProductionService;
use App\Services\RegionService;
use Mockery;
use ReflectionClass;
use Tests\TestCase;

class GeneralProductionServiceTest extends TestCase
{
    public function test_build_weekly_payload_batches_month_query_per_bank()
    {
        $repository = Mockery::mock(GeneralProductionRepository::class);
        $repository->shouldReceive('listBanks')->once()->andReturn(array(
            array('banco_id' => 1, 'banco_name' => 'Bank A', 'banco_class' => ''),
        ));
        $repository->shouldReceive('listFinancialMetasByBankMonthYear')->once()->andReturn(array(
            array(
                'meta_valor' => 100.0,
                'def_sem' => 'N',
                'sem_1' => 0,
                'sem_2' => 0,
                'sem_3' => 0,
                'sem_4' => 0,
                'sem_5' => 0,
                'anda_neo' => 'F1',
            ),
        ));
        $repository->shouldReceive('listCarteiraCodesByBankId')->once()->with(1)->andReturn(array('C1'));
        $repository->shouldReceive('findCarteiraModeByBankId')->once()->with(1)->andReturn('=');
        $repository->shouldReceive('findAreaNameById')->never();

        $neoRepository = Mockery::mock(GeneralProductionNeoRepository::class);
        $neoRepository->shouldReceive('listFinancialMonthEvents')->once()->andReturn(array(
            array('type_name' => 'F1', 'code' => '10', 'value' => 25.0),
            array('type_name' => 'F1', 'code' => '11', 'value' => 15.0),
        ));
        $neoRepository->shouldNotReceive('sumFinancialByMonth');

        $regionService = Mockery::mock(RegionService::class);
        $regionService->shouldReceive('listUserRegions')->once()->andReturn(array());

        $service = new GeneralProductionService(
            $repository,
            $neoRepository,
            $regionService,
            new PerformanceMetricFormatter()
        );

        $context = $this->buildContext($service);
        $reflection = new ReflectionClass($service);
        $method = $reflection->getMethod('buildWeeklyPayload');
        $method->setAccessible(true);

        $payload = $method->invoke($service, $context);

        $this->assertCount(1, $payload['rows']);
        $row = $payload['rows'][0]->toArray();
        $this->assertSame(40.0, $row['realized']);
        $this->assertSame(array('10', '11'), $row['codes']);
    }

    public function test_build_monthly_payload_batches_month_week_query_per_bank()
    {
        $repository = Mockery::mock(GeneralProductionRepository::class);
        $repository->shouldReceive('findWeekByMonthYear')->once()->andReturn(array(
            'ini_1' => 1, 'fim_1' => 7,
            'ini_2' => 8, 'fim_2' => 14,
            'ini_3' => 15, 'fim_3' => 21,
            'ini_4' => 22, 'fim_4' => 28,
            'ini_5' => 29, 'fim_5' => 31,
        ));
        $repository->shouldReceive('listBanks')->once()->andReturn(array(
            array('banco_id' => 1, 'banco_name' => 'Bank A', 'banco_class' => ''),
        ));
        $repository->shouldReceive('listFinancialMetasByBankMonthYear')->once()->andReturn(array(
            array(
                'meta_valor' => 100.0,
                'def_sem' => 'N',
                'sem_1' => 0,
                'sem_2' => 0,
                'sem_3' => 0,
                'sem_4' => 0,
                'sem_5' => 0,
                'anda_neo' => 'F1',
            ),
        ));
        $repository->shouldReceive('listCarteiraCodesByBankId')->once()->with(1)->andReturn(array('C1'));
        $repository->shouldReceive('findCarteiraModeByBankId')->once()->with(1)->andReturn('=');

        $neoRepository = Mockery::mock(GeneralProductionNeoRepository::class);
        $neoRepository->shouldReceive('listFinancialWeekMonthEvents')->once()->andReturn(array(
            array('type_name' => 'F1', 'code' => '10', 'value' => 30.0, 'day_number' => 3),
            array('type_name' => 'F1', 'code' => '11', 'value' => 20.0, 'day_number' => 10),
        ));
        $neoRepository->shouldNotReceive('sumFinancialByWeek');

        $regionService = Mockery::mock(RegionService::class);
        $regionService->shouldReceive('listUserRegions')->once()->andReturn(array());

        $service = new GeneralProductionService(
            $repository,
            $neoRepository,
            $regionService,
            new PerformanceMetricFormatter()
        );

        $context = $this->buildContext($service);
        $reflection = new ReflectionClass($service);
        $method = $reflection->getMethod('buildMonthlyPayload');
        $method->setAccessible(true);

        $payload = $method->invoke($service, $context);

        $this->assertCount(1, $payload['rows']);
        $row = $payload['rows'][0]->toArray();
        $this->assertSame(50.0, $row['totalReal']);
        $this->assertSame(30.0, $row['weekData'][0]['real']);
        $this->assertSame(20.0, $row['weekData'][1]['real']);
    }

    private function buildContext(GeneralProductionService $service)
    {
        $reflection = new ReflectionClass($service);
        $method = $reflection->getMethod('buildContext');
        $method->setAccessible(true);

        return $method->invoke($service, array(
            'startDate' => 'July / 2026',
            'startSetor' => '',
            'mes' => 7,
            'ano' => 2026,
            'regiao_id' => 0,
        ), array(
            'usuarioSetor' => 0,
            'usuarioCliente' => '',
            'usuarioID' => 1,
            'usuarioNivel' => 'ADM',
            'usuarioRegiaoModo' => 'N',
        ));
    }
}
