<?php

namespace Tests\Unit;

use App\Http\Controllers\WeekController;
use App\Http\Requests\WeekStoreRequest;
use App\Http\Requests\WeekUpdateRequest;
use App\Services\WeekService;
use App\Support\View;
use Mockery;
use Tests\TestCase;

class WeekControllerTest extends TestCase
{
    public function test_show_returns_loaded_json_payload()
    {
        $service = Mockery::mock(WeekService::class);
        $service->shouldReceive('findById')->once()->with(8)->andReturn(array(
            'semanas_id' => 8,
            'mes' => 7,
            'ano' => 2026,
        ));

        $controller = new WeekController($service, app(View::class));
        $response = $controller->show(8);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('loaded', $response->getData(true)['code']);
        $this->assertSame(8, $response->getData(true)['data']['semanas_id']);
    }

    public function test_store_returns_success_json_payload()
    {
        $service = Mockery::mock(WeekService::class);
        $service->shouldReceive('createFromRequest')->once()->andReturn('1');

        $controller = new WeekController($service, app(View::class));
        $request = WeekStoreRequest::create('/***REMOVED***/semanas', 'POST', array(
            'mes_sem' => 7,
            'ano_sem' => 2026,
            'ini1_sem' => 1,
            'fim1_sem' => 7,
            'ini2_sem' => 8,
            'fim2_sem' => 14,
            'ini3_sem' => 15,
            'fim3_sem' => 21,
            'ini4_sem' => 22,
            'fim4_sem' => 28,
        ));

        $response = $controller->store($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('success', $response->getData(true)['code']);
    }

    public function test_update_returns_success_json_payload()
    {
        $service = Mockery::mock(WeekService::class);
        $service->shouldReceive('updateFromRequest')
            ->once()
            ->with(Mockery::on(function ($input) {
                return isset($input['id_sem']) && (int) $input['id_sem'] === 8;
            }))
            ->andReturn('1');

        $controller = new WeekController($service, app(View::class));
        $request = WeekUpdateRequest::create('/***REMOVED***/semanas/8', 'PUT', array(
            'mes_sem' => 7,
            'ano_sem' => 2026,
            'ini1_sem' => 1,
            'fim1_sem' => 7,
            'ini2_sem' => 8,
            'fim2_sem' => 14,
            'ini3_sem' => 15,
            'fim3_sem' => 21,
            'ini4_sem' => 22,
            'fim4_sem' => 28,
        ));

        $response = $controller->update($request, 8);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('success', $response->getData(true)['code']);
    }

    public function test_destroy_returns_success_json_payload()
    {
        $service = Mockery::mock(WeekService::class);
        $service->shouldReceive('delete')->once()->with(8)->andReturn(true);

        $controller = new WeekController($service, app(View::class));
        $response = $controller->destroy(8);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('success', $response->getData(true)['code']);
    }
}
