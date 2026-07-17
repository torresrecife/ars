<?php

namespace Tests\Unit;

use App\Http\Controllers\SectorAdminController;
use App\Services\SectorAdminService;
use App\Support\View;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

class SectorAdminControllerTest extends TestCase
{
    public function test_show_returns_loaded_json_payload()
    {
        $service = Mockery::mock(SectorAdminService::class);
        $service->shouldReceive('editPayload')->once()->with(3)->andReturn('3-|-Operacional-|-2026-07-17');

        $controller = new SectorAdminController($service, app(View::class));
        $response = $controller->show(3);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('loaded', $response->getData(true)['code']);
        $this->assertSame(3, $response->getData(true)['data']['area_id']);
    }

    public function test_store_returns_success_json_payload()
    {
        $service = Mockery::mock(SectorAdminService::class);
        $service->shouldReceive('create')->once()->andReturn('1');

        $controller = new SectorAdminController($service, app(View::class));
        $request = Request::create('/admin/setores', 'POST', array('area_nome' => 'Operacional'));

        $response = $controller->store($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('success', $response->getData(true)['code']);
    }

    public function test_update_returns_success_json_payload()
    {
        $service = Mockery::mock(SectorAdminService::class);
        $service->shouldReceive('update')
            ->once()
            ->with(Mockery::on(function ($input) {
                return isset($input['area_id']) && (int) $input['area_id'] === 3;
            }))
            ->andReturn('1');

        $controller = new SectorAdminController($service, app(View::class));
        $request = Request::create('/admin/setores/3', 'PUT', array('area_nome' => 'Operacional'));

        $response = $controller->update($request, 3);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('success', $response->getData(true)['code']);
    }

    public function test_destroy_returns_success_json_payload()
    {
        $service = Mockery::mock(SectorAdminService::class);
        $service->shouldReceive('delete')->once()->with(3)->andReturn('1');

        $controller = new SectorAdminController($service, app(View::class));
        $response = $controller->destroy(3);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('success', $response->getData(true)['code']);
    }
}
