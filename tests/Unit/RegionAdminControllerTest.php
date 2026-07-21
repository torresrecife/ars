<?php

namespace Tests\Unit;

use App\Http\Controllers\RegionAdminController;
use App\Http\Requests\RegionStoreRequest;
use App\Http\Requests\RegionUpdateRequest;
use App\Services\RegionAdminService;
use App\Support\View;
use App\Support\WriteResult;
use Mockery;
use Tests\TestCase;

class RegionAdminControllerTest extends TestCase
{
    public function test_show_returns_loaded_json_payload()
    {
        $service = Mockery::mock(RegionAdminService::class);
        $service->shouldReceive('editPayload')
            ->once()
            ->with(4)
            ->andReturn(array(
                'regiao_id' => 4,
                'regiao_nome' => 'Sul',
                'regiao_slug' => 'sul',
                'ufs' => array('PR', 'SC'),
            ));

        $controller = new RegionAdminController($service, app(View::class));
        $response = $controller->show(4);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('loaded', $response->getData(true)['code']);
        $this->assertSame(4, $response->getData(true)['data']['regiao_id']);
    }

    public function test_store_returns_success_json_payload()
    {
        $service = Mockery::mock(RegionAdminService::class);
        $service->shouldReceive('create')->once()->andReturn(WriteResult::success());

        $controller = new RegionAdminController($service, app(View::class));
        $request = RegionStoreRequest::create('/***REMOVED***/regioes', 'POST', array(
            'regiao_nome' => 'Sul',
            'regiao_slug' => 'sul',
            'regiao_status' => 'Y',
            'regiao_ufs' => 'PR,SC',
        ));

        $response = $controller->store($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('success', $response->getData(true)['code']);
    }

    public function test_update_returns_success_json_payload()
    {
        $service = Mockery::mock(RegionAdminService::class);
        $service->shouldReceive('update')
            ->once()
            ->with(Mockery::on(function ($input) {
                return isset($input['regiao_id']) && (int) $input['regiao_id'] === 4;
            }))
            ->andReturn(WriteResult::success());

        $controller = new RegionAdminController($service, app(View::class));
        $request = RegionUpdateRequest::create('/***REMOVED***/regioes/4', 'PUT', array(
            'regiao_nome' => 'Sul',
            'regiao_slug' => 'sul',
            'regiao_status' => 'Y',
            'regiao_ufs' => 'PR,SC',
        ));

        $response = $controller->update($request, 4);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('success', $response->getData(true)['code']);
    }

    public function test_destroy_returns_linked_users_conflict_when_service_returns_linked_users_result()
    {
        $service = Mockery::mock(RegionAdminService::class);
        $service->shouldReceive('delete')->once()->with(4)->andReturn(WriteResult::linkedUsers());

        $controller = new RegionAdminController($service, app(View::class));
        $response = $controller->destroy(4);

        $this->assertSame(409, $response->getStatusCode());
        $this->assertSame('linked_users', $response->getData(true)['code']);
    }
}
