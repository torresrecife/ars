<?php

namespace Tests\Unit;

use App\Data\DashboardPanelInput;
use App\Data\GeneralProductionInput;
use App\Http\Requests\DashboardPanelRequest;
use App\Http\Requests\GeneralProductionRequest;
use Tests\TestCase;

class ProductionRequestDtoTest extends TestCase
{
    public function test_dashboard_panel_input_normalizes_payload()
    {
        $dto = DashboardPanelInput::fromArray(array(
            'bank_id' => 7,
            'area_id' => '4',
            'mes' => 7,
            'ano' => 2026,
            'regiao_id' => 2,
        ));

        $this->assertSame(
            array(
                'bank_id' => 7,
                'area_id' => '4',
                'mes' => 7,
                'ano' => 2026,
                'regiao_id' => 2,
            ),
            $dto->toArray()
        );
    }

    public function test_general_production_input_normalizes_payload()
    {
        $dto = GeneralProductionInput::fromArray(array(
            'startDate' => __('July') . ' / 2026',
            'startSetor' => '3',
            'mes' => 7,
            'ano' => 2026,
            'regiao_id' => 2,
        ));

        $this->assertSame(
            array(
                'startDate' => __('July') . ' / 2026',
                'startSetor' => '3',
                'mes' => 7,
                'ano' => 2026,
                'regiao_id' => 2,
            ),
            $dto->toArray()
        );
    }

    public function test_dashboard_panel_request_accepts_valid_values()
    {
        $request = DashboardPanelRequest::create('/painel', 'GET', array(
            'bank_id' => 7,
            'area_id' => '4',
            'mes' => 7,
            'ano' => 2026,
            'regiao_id' => 2,
        ));

        $validator = app('validator')->make($request->all(), (new DashboardPanelRequest())->rules());

        $this->assertFalse($validator->fails());
    }

    public function test_general_production_request_accepts_valid_values()
    {
        $request = GeneralProductionRequest::create('/relatorio', 'GET', array(
            'startDate' => __('July') . ' / 2026',
            'startSetor' => '3',
            'mes' => 7,
            'ano' => 2026,
            'regiao_id' => 2,
        ));

        $validator = app('validator')->make($request->all(), (new GeneralProductionRequest())->rules());

        $this->assertFalse($validator->fails());
    }
}
