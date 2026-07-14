<?php

namespace App\Providers;

use App\Repositories\MainPageRepository;
use App\Repositories\MetaRepository;
use App\Repositories\RegionAdminRepository;
use App\Repositories\RegionRepository;
use App\Repositories\SectorAdminRepository;
use App\Repositories\ClientAdminRepository;
use App\Repositories\AndamentoAdminRepository;
use App\Repositories\DashboardRepository;
use App\Repositories\GeneralProductionRepository;
use App\Repositories\GeneralProductionNeoRepository;
use App\Repositories\NeoDetailRepository;
use App\Repositories\NeoPanelRepository;
use App\Repositories\UserAdminRepository;
use App\Repositories\WeekRepository;
use App\Services\ClientAdminService;
use App\Services\AndamentoAdminService;
use App\Services\DashboardPanelService;
use App\Services\MetaService;
use App\Services\MainPageService;
use App\Services\GeneralProductionService;
use App\Services\NeoDetailService;
use App\Services\RegionAdminService;
use App\Services\RegionService;
use App\Services\SectorAdminService;
use App\Services\UserAdminService;
use App\Services\WeekService;
use App\Support\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    private function legacySqlsrvConnection($app)
    {
        $legacyApp = $app->make('legacy.application');

        if (!function_exists('sqlsrv_connect')) {
            return null;
        }

        try {
            return $legacyApp->db()->sqlsrv();
        } catch (\RuntimeException $exception) {
            return null;
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('legacy.application', function () {
            return require base_path('bootstrap/legacy_app.php');
        });

        $this->app->singleton('legacy.mysql', function ($app) {
            return $app->make('legacy.application')->db()->mysql();
        });

        $this->app->singleton(View::class, function () {
            return new View(base_path());
        });

        $this->app->singleton(RegionService::class, function ($app) {
            return new RegionService(
                new RegionRepository($app->make('legacy.mysql'))
            );
        });

        $this->app->singleton(RegionAdminService::class, function ($app) {
            return new RegionAdminService(
                new RegionAdminRepository($app->make('legacy.mysql'))
            );
        });

        $this->app->singleton(WeekService::class, function ($app) {
            return new WeekService(
                new WeekRepository($app->make('legacy.mysql'))
            );
        });

        $this->app->singleton(MetaService::class, function ($app) {
            return new MetaService(
                new MetaRepository($app->make('legacy.mysql')),
                $app->make(RegionService::class)
            );
        });

        $this->app->singleton(UserAdminService::class, function ($app) {
            return new UserAdminService(
                new UserAdminRepository($app->make('legacy.mysql')),
                $app->make(RegionService::class)
            );
        });

        $this->app->singleton(SectorAdminService::class, function ($app) {
            return new SectorAdminService(
                new SectorAdminRepository($app->make('legacy.mysql'))
            );
        });

        $this->app->singleton(ClientAdminService::class, function ($app) {
            return new ClientAdminService(
                new ClientAdminRepository(
                    $app->make('legacy.mysql'),
                    function_exists('ars_sqlsrv_connection') ? ars_sqlsrv_connection() : null
                )
            );
        });

        $this->app->singleton(AndamentoAdminService::class, function ($app) {
            return new AndamentoAdminService(
                new AndamentoAdminRepository($app->make('legacy.mysql'))
            );
        });

        $this->app->singleton(DashboardPanelService::class, function ($app) {
            $months = isset($GLOBALS['arrMonths']) && is_array($GLOBALS['arrMonths'])
                ? $GLOBALS['arrMonths']
                : array(1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'MarÃ§o', 4 => 'Abril', 5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto', 9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro');

            return new DashboardPanelService(
                new DashboardRepository($app->make('legacy.mysql')),
                new NeoPanelRepository($this->legacySqlsrvConnection($app)),
                $app->make(RegionService::class),
                $months
            );
        });

        $this->app->singleton(NeoDetailService::class, function ($app) {
            return new NeoDetailService(
                new NeoDetailRepository($this->legacySqlsrvConnection($app)),
                new DashboardRepository($app->make('legacy.mysql')),
                $app->make(RegionService::class)
            );
        });

        $this->app->singleton(GeneralProductionService::class, function ($app) {
            return new GeneralProductionService(
                new GeneralProductionRepository($app->make('legacy.mysql')),
                new GeneralProductionNeoRepository($this->legacySqlsrvConnection($app)),
                $app->make(RegionService::class)
            );
        });

        $this->app->singleton(MainPageService::class, function ($app) {
            $months = isset($GLOBALS['arrMonths']) && is_array($GLOBALS['arrMonths'])
                ? $GLOBALS['arrMonths']
                : array(1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril', 5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto', 9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro');

            return new MainPageService(
                new MainPageRepository($app->make('legacy.mysql')),
                $app->make(RegionService::class),
                $months
            );
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        require_once base_path('inc/functions.php');
        require_once base_path('inc/somadias.php');
    }
}
