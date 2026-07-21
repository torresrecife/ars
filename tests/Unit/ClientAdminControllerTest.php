<?php

namespace Tests\Unit;

use App\Http\Controllers\ClientAdminController;
use App\Http\Requests\ClientStoreRequest;
use App\Http\Requests\ClientUpdateRequest;
use App\Services\ClientAdminService;
use App\Support\View;
use App\Support\WriteResult;
use Mockery;
use Tests\TestCase;

class ClientAdminControllerTest extends TestCase
{
    public function test_show_returns_loaded_json_payload()
    {
        $service = Mockery::mock(ClientAdminService::class);
        $service->shouldReceive('editPayload')
            ->once()
            ->with(5)
            ->andReturn(array(
                'banco_id' => 5,
                'banco_name' => 'Cliente XP',
                'banco_cod' => 'XP01',
                'banco_creator' => '***REMOVED***',
                'banco_area' => 2,
                'banco_status' => 'Y',
                'banco_class' => 'A',
                'simulador' => '10',
                'banco_curto' => 'XP',
                'dados_codes' => array('001', '002'),
            ));

        $controller = new ClientAdminController($service, app(View::class));
        $response = $controller->show(5);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('loaded', $response->getData(true)['code']);
        $this->assertSame(5, $response->getData(true)['data']['banco_id']);
    }

    public function test_store_returns_success_json_payload()
    {
        $service = Mockery::mock(ClientAdminService::class);
        $service->shouldReceive('create')->once()->andReturn(WriteResult::success());

        $controller = new ClientAdminController($service, app(View::class));
        $request = ClientStoreRequest::create('/***REMOVED***/clientes', 'POST', array(
            'banco_name' => 'Cliente XP',
            'banco_cod' => 'XP01',
            'banco_area' => 2,
            'banco_status' => 'Y',
            'banco_class' => 'A',
            'simulador' => '10',
            'banco_curto' => 'XP',
        ));

        $response = $controller->store($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('success', $response->getData(true)['code']);
    }

    public function test_update_returns_success_json_payload()
    {
        $service = Mockery::mock(ClientAdminService::class);
        $service->shouldReceive('update')
            ->once()
            ->with(Mockery::on(function ($input) {
                return isset($input['banco_id']) && (int) $input['banco_id'] === 5;
            }))
            ->andReturn(WriteResult::success());

        $controller = new ClientAdminController($service, app(View::class));
        $request = ClientUpdateRequest::create('/***REMOVED***/clientes/5', 'PUT', array(
            'banco_name' => 'Cliente XP',
            'banco_cod' => 'XP01',
            'banco_area' => 2,
            'banco_status' => 'Y',
            'banco_class' => 'A',
            'simulador' => '10',
            'banco_curto' => 'XP',
        ));

        $response = $controller->update($request, 5);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('success', $response->getData(true)['code']);
    }

    public function test_destroy_returns_success_json_payload()
    {
        $service = Mockery::mock(ClientAdminService::class);
        $service->shouldReceive('delete')->once()->with(5)->andReturn(WriteResult::success());

        $controller = new ClientAdminController($service, app(View::class));
        $response = $controller->destroy(5);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('success', $response->getData(true)['code']);
    }
}
