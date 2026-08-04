<?php

namespace Tests\Unit;

use App\Data\NeoDetailInput;
use App\Http\Requests\NeoDetailRequest;
use App\Repositories\DashboardRepository;
use App\Repositories\NeoDetailRepository;
use App\Services\NeoDetailService;
use App\Services\RegionService;
use App\ViewModels\AndamentoDetailViewData;
use App\ViewModels\FinancialDetailViewData;
use App\ViewModels\NeoDetailRow;
use Mockery;
use ReflectionClass;
use Tests\TestCase;

class NeoDetailContractsTest extends TestCase
{
    public function test_detail_input_normalizes_payload()
    {
        $dto = NeoDetailInput::fromArray(array(
            'codig_and' => '1,2',
            'banco_and' => 'Banco A',
            'detail_bank_id' => 7,
            'detail_anda_id' => 9,
            'detail_month' => 7,
            'detail_year' => 2026,
            'detail_week' => '2',
            'detail_region_id' => 3,
        ));

        $this->assertSame('Banco A', $dto->bankNameForAndamento());
        $this->assertSame(
            array(
                'codig_and' => '1,2',
                'banco_and' => 'Banco A',
                'codig_lnc' => '',
                'banco_lnc' => '',
                'detail_bank_id' => 7,
                'detail_anda_id' => 9,
                'detail_month' => 7,
                'detail_year' => 2026,
                'detail_week' => '2',
                'detail_region_id' => 3,
            ),
            $dto->toArray()
        );
    }

    public function test_detail_request_accepts_valid_values()
    {
        $request = NeoDetailRequest::create('/detalhes/andamentos', 'GET', array(
            'codig_and' => '1,2',
            'banco_and' => 'Banco A',
            'codig_lnc' => '4,5',
            'banco_lnc' => 'Banco B',
            'detail_bank_id' => 7,
            'detail_anda_id' => 9,
            'detail_month' => 7,
            'detail_year' => 2026,
            'detail_week' => 'total',
            'detail_region_id' => 3,
        ));

        $validator = app('validator')->make($request->all(), (new NeoDetailRequest())->rules());

        $this->assertFalse($validator->fails());
    }

    public function test_detail_view_data_normalizes_rows()
    {
        $row = new NeoDetailRow(array(
            'Codigo' => 10,
            'comarca_exibicao' => 'Toronto',
        ));

        $andamento = new AndamentoDetailViewData(array($row), 'Banco A', 1);
        $financial = new FinancialDetailViewData(array($row), 'Banco B', 1, 120.5);

        $this->assertSame('Banco A', $andamento->toArray()['bankName']);
        $this->assertSame(10, $andamento->toArray()['rows'][0]['Codigo']);
        $this->assertSame(120.5, $financial->toArray()['totalValue']);
    }

    public function test_service_formats_cnj_number_when_helper_is_unavailable()
    {
        $service = new NeoDetailService(
            Mockery::mock(NeoDetailRepository::class),
            Mockery::mock(DashboardRepository::class),
            Mockery::mock(RegionService::class)
        );

        $reflection = new ReflectionClass($service);
        $method = $reflection->getMethod('formatProcesso');
        $method->setAccessible(true);

        $this->assertSame(
            '0005734-19.2025.8.17.2810',
            $method->invoke($service, '00057341920258172810')
        );
    }
}
