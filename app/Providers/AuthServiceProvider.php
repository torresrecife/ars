<?php

namespace App\Providers;

use App\Auth\LegacyUsuarioProvider;
use App\Models\Andamento;
use App\Models\Area;
use App\Models\Banco;
use App\Models\MetaAndamento;
use App\Models\Regiao;
use App\Models\Semana;
use App\Models\Usuario;
use App\Policies\MetaAdminPolicy;
use App\Policies\SystemAdminPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Usuario::class => SystemAdminPolicy::class,
        Area::class => SystemAdminPolicy::class,
        Banco::class => SystemAdminPolicy::class,
        Andamento::class => SystemAdminPolicy::class,
        Semana::class => SystemAdminPolicy::class,
        Regiao::class => SystemAdminPolicy::class,
        MetaAndamento::class => MetaAdminPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Auth::provider('legacy-eloquent', function ($app, array $config) {
            return new LegacyUsuarioProvider($app['hash'], $config['model']);
        });

        Gate::define('access-admin', function ($user) {
            return in_array((string) $user->nivel_usu, array('ADM', 'GER'), true);
        });
    }
}
