<?php

namespace Tests\Unit;

use App\Repositories\MainPageRepository;
use App\Services\MainPageService;
use App\Services\RegionService;
use App\ViewModels\MainPageViewData;
use Mockery;
use Tests\TestCase;

class MainPageServiceTest extends TestCase
{
    public function test_build_returns_typed_view_data_for_***REMOVED***_section()
    {
        $repository = Mockery::mock(MainPageRepository::class);
        $repository->shouldReceive('listAdminBanks')
            ->once()
            ->with(3, '1,2')
            ->andReturn(array(array('id_bco' => 10, 'nome_bco' => 'Banco X')));

        $regionService = Mockery::mock(RegionService::class);
        $regionService->shouldReceive('listUserRegions')
            ->once()
            ->with(9)
            ->andReturn(array());

        $service = new MainPageService($repository, $regionService, array(
            7 => __('July'),
        ));

        $result = $service->build(array(
            'section' => '***REMOVED***',
            'mes' => 7,
            'ano' => 2026,
        ), array(
            'usuarioID' => 9,
            'usuarioSetor' => 3,
            'usuarioCliente' => '1,2',
            'usuarioNivel' => 'ADM',
        ));

        $this->assertInstanceOf(MainPageViewData::class, $result);
        $this->assertSame('***REMOVED***', $result->currentSection());
        $this->assertTrue($result->canAdmin());
        $this->assertNull($result->topAction());
        $this->assertSame('view', $result->content()->type());
        $this->assertSame('***REMOVED***/index', $result->content()->view());
        $this->assertSame(__('July') . ' / 2026', $result->monthYearLabel());
    }

    public function test_build_returns_top_action_for_usuarios_section()
    {
        $repository = Mockery::mock(MainPageRepository::class);
        $regionService = Mockery::mock(RegionService::class);
        $regionService->shouldReceive('listUserRegions')
            ->once()
            ->with(9)
            ->andReturn(array());

        $service = new MainPageService($repository, $regionService, array(
            7 => __('July'),
        ));

        $result = $service->build(array(
            'section' => 'usuarios',
            'mes' => 7,
            'ano' => 2026,
        ), array(
            'usuarioID' => 9,
            'usuarioSetor' => 3,
            'usuarioCliente' => '1,2',
            'usuarioNivel' => 'ADM',
        ));

        $this->assertSame('usuarios', $result->currentSection());
        $this->assertNotNull($result->topAction());
        $this->assertSame(__('New User'), $result->topAction()->label());
        $this->assertSame('fc_edit_usu("", "I");', $result->topAction()->javascript());
        $this->assertSame('controller', $result->content()->type());
        $this->assertSame('user-***REMOVED***', $result->content()->controller());
    }
}
