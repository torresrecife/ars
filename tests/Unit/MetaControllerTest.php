<?php

namespace Tests\Unit;

use App\Http\Controllers\MetaController;
use App\Http\Requests\MetaStoreRequest;
use App\Http\Requests\MetaUpdateRequest;
use App\Services\MetaService;
use App\Support\View;
use Mockery;
use Tests\TestCase;

class MetaControllerTest extends TestCase
{
    public function test_show_returns_loaded_json_payload()
    {
        $service = Mockery::mock(MetaService::class);
        $service->shouldReceive('findById')
            ->once()
            ->with(11)
            ->andReturn(array(
                'meta_id' => 11,
                'banco_id' => 4,
                'meta_mes' => 7,
                'meta_ano' => 2026,
                'anda_id' => 5,
                'meta_valor' => '20',
            ));

        $controller = new MetaController($service, app(View::class));
        $response = $controller->show(11);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('loaded', $response->getData(true)['code']);
        $this->assertSame(11, $response->getData(true)['data']['meta_id']);
    }

    public function test_store_returns_success_json_payload()
    {
        $service = Mockery::mock(MetaService::class);
        $service->shouldReceive('createManyFromRequest')->once()->andReturn('1');

        $controller = new MetaController($service, app(View::class));
        $request = MetaStoreRequest::create('/admin/metas', 'POST', array(
            'banco_id' => 4,
            'meta_mes' => 7,
            'meta_ano' => 2026,
            'numes' => 1,
            'meta_name_1' => 5,
            'regiao_id_1' => '',
            'meta_valor_1' => '20',
            'def_sem_1' => 'N',
            'sem1_valor_1' => '',
            'sem2_valor_1' => '',
            'sem3_valor_1' => '',
            'sem4_valor_1' => '',
            'sem5_valor_1' => '',
        ));

        $response = $controller->store($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('success', $response->getData(true)['code']);
    }

    public function test_update_returns_success_json_payload()
    {
        $service = Mockery::mock(MetaService::class);
        $service->shouldReceive('updateManyFromRequest')
            ->once()
            ->with(Mockery::on(function ($input) {
                return isset($input['meta_id']) && (int) $input['meta_id'] === 11;
            }))
            ->andReturn('1');

        $controller = new MetaController($service, app(View::class));
        $request = MetaUpdateRequest::create('/admin/metas/11', 'PUT', array(
            'banco_id' => 4,
            'meta_mes' => 7,
            'meta_ano' => 2026,
            'numes' => 1,
            'meta_name_1' => 5,
            'regiao_id_1' => '',
            'meta_valor_1' => '20',
            'def_sem_1' => 'N',
            'sem1_valor_1' => '',
            'sem2_valor_1' => '',
            'sem3_valor_1' => '',
            'sem4_valor_1' => '',
            'sem5_valor_1' => '',
        ));

        $response = $controller->update($request, 11);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('success', $response->getData(true)['code']);
    }

    public function test_destroy_returns_success_json_payload()
    {
        $service = Mockery::mock(MetaService::class);
        $service->shouldReceive('delete')->once()->with(11)->andReturn(true);

        $controller = new MetaController($service, app(View::class));
        $response = $controller->destroy(11);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('success', $response->getData(true)['code']);
    }
}
