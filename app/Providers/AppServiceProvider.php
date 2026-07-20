<?php

namespace App\Providers;

use App\Domain\Metrics\PerformanceMetricFormatter;
use App\Infrastructure\Database\SqlsrvConnectionFactory;
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
use App\Repositories\SqlsrvLookupRepository;
use App\Repositories\UserRepository;
use App\Repositories\UserAdminRepository;
use App\Repositories\WeekRepository;
use App\Services\AuthService;
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
    private function monthsMap()
    {
        return array(
            1 => __('January'),
            2 => __('February'),
            3 => __('March'),
            4 => __('April'),
            5 => __('May'),
            6 => __('June'),
            7 => __('July'),
            8 => __('August'),
            9 => __('September'),
            10 => __('October'),
            11 => __('November'),
            12 => __('December'),
        );
    }

    private function sqlsrvConnection()
    {
        if (!function_exists('sqlsrv_connect')) {
            return null;
        }

        try {
            return (new SqlsrvConnectionFactory((array) config('database.connections.sqlsrv', array())))->make();
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
        $this->app->singleton('legacy.sqlsrv', function ($app) {
            return $this->sqlsrvConnection();
        });

        $this->app->singleton(View::class, function () {
            return new View(base_path());
        });

        $this->app->singleton(SqlsrvLookupRepository::class, function ($app) {
            return new SqlsrvLookupRepository($app->make('legacy.sqlsrv'));
        });

        $this->app->singleton(PerformanceMetricFormatter::class, function () {
            return new PerformanceMetricFormatter();
        });

        $this->app->singleton(AuthService::class, function ($app) {
            return new AuthService(
                new UserRepository(),
                new RegionRepository()
            );
        });

        $this->app->singleton(RegionService::class, function ($app) {
            return new RegionService(
                new RegionRepository()
            );
        });

        $this->app->singleton(RegionAdminService::class, function ($app) {
            return new RegionAdminService(
                new RegionAdminRepository()
            );
        });

        $this->app->singleton(WeekService::class, function ($app) {
            return new WeekService(
                new WeekRepository()
            );
        });

        $this->app->singleton(MetaService::class, function ($app) {
            return new MetaService(
                new MetaRepository(),
                $app->make(RegionService::class)
            );
        });

        $this->app->singleton(UserAdminService::class, function ($app) {
            return new UserAdminService(
                new UserAdminRepository(),
                $app->make(RegionService::class)
            );
        });

        $this->app->singleton(SectorAdminService::class, function ($app) {
            return new SectorAdminService(
                new SectorAdminRepository()
            );
        });

        $this->app->singleton(ClientAdminService::class, function ($app) {
            return new ClientAdminService(
                new ClientAdminRepository(
                    $app->make(SqlsrvLookupRepository::class)
                )
            );
        });

        $this->app->singleton(AndamentoAdminService::class, function ($app) {
            return new AndamentoAdminService(
                new AndamentoAdminRepository()
            );
        });

        $this->app->singleton(DashboardPanelService::class, function ($app) {
            return new DashboardPanelService(
                new DashboardRepository(),
                new NeoPanelRepository($app->make('legacy.sqlsrv')),
                $app->make(RegionService::class),
                $this->monthsMap(),
                $app->make(PerformanceMetricFormatter::class)
            );
        });

        $this->app->singleton(NeoDetailService::class, function ($app) {
            return new NeoDetailService(
                new NeoDetailRepository($app->make('legacy.sqlsrv')),
                new DashboardRepository(),
                $app->make(RegionService::class)
            );
        });

        $this->app->singleton(GeneralProductionService::class, function ($app) {
            return new GeneralProductionService(
                new GeneralProductionRepository(),
                new GeneralProductionNeoRepository($app->make('legacy.sqlsrv')),
                $app->make(RegionService::class),
                $app->make(PerformanceMetricFormatter::class)
            );
        });

        $this->app->singleton(MainPageService::class, function ($app) {
            return new MainPageService(
                new MainPageRepository(),
                $app->make(RegionService::class),
                $this->monthsMap()
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
    }
}

