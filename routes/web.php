<?php

use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/', 'AuthController@showLogin');
    Route::get('/login', 'AuthController@showLogin')->name('login');
    Route::post('/login', 'AuthController@login')->name('login.submit');
});

Route::middleware('auth')->group(function () {
    Route::get('/index', 'HomeController@webIndex')->name('legacy.home');
    Route::get('/logout', 'AuthController@logout')->name('logout');
    Route::post('/ajax/newpass', 'AuthController@updateOwnPassword')->name('ajax.newpass');
    Route::post('/ajax/select', 'SelectController@webAjax')->name('ajax.select');
    Route::match(['get', 'post'], '/detalhes/andamentos', 'AndamentoDetailController@webIndex')->name('detalhes.andamentos');
    Route::match(['get', 'post'], '/detalhes/faturamento', 'FinancialDetailController@webIndex')->name('detalhes.faturamento');

    Route::get('/carteiras', 'HomeController@webSectionPage')->defaults('section', 'carteiras')->name('carteiras');
    Route::get('/painel', 'HomeController@webSectionPage')->defaults('section', 'painel')->name('painel');
    Route::get('/producao', 'HomeController@webSectionPage')->defaults('section', 'producao')->name('producao');
    Route::get('/relatorio', 'HomeController@webRelatorio')->name('relatorio');
    Route::middleware('can:access-***REMOVED***')->group(function () {
        Route::get('/***REMOVED***', 'HomeController@webSectionPage')->defaults('section', '***REMOVED***')->name('***REMOVED***');
        Route::get('/metas', 'HomeController@webMetas')->middleware('can:viewAny,App\\Models\\MetaAndamento')->name('metas');
    });

    Route::group(array('middleware' => 'can:viewAny,App\\Models\\Usuario'), function () {
        Route::get('/usuarios', 'HomeController@webSectionPage')->defaults('section', 'usuarios')->name('usuarios');
    });
    Route::group(array('middleware' => 'can:viewAny,App\\Models\\Area'), function () {
        Route::get('/setores', 'HomeController@webSectionPage')->defaults('section', 'setores')->name('setores');
    });
    Route::group(array('middleware' => 'can:viewAny,App\\Models\\Banco'), function () {
        Route::get('/clientes', 'HomeController@webSectionPage')->defaults('section', 'clientes')->name('clientes');
    });
    Route::group(array('middleware' => 'can:viewAny,App\\Models\\Andamento'), function () {
        Route::get('/andamentos', 'HomeController@webSectionPage')->defaults('section', 'andamentos')->name('andamentos');
    });
    Route::group(array('middleware' => 'can:viewAny,App\\Models\\Semana'), function () {
        Route::get('/semanas', 'HomeController@webSectionPage')->defaults('section', 'semanas')->name('semanas');
    });
    Route::group(array('middleware' => 'can:viewAny,App\\Models\\Regiao'), function () {
        Route::get('/regioes', 'HomeController@webSectionPage')->defaults('section', 'regioes')->name('regioes');
    });

    Route::prefix('***REMOVED***')->as('***REMOVED***.')->group(function () {
        Route::group(array('middleware' => 'can:viewAny,App\\Models\\Usuario'), function () {
            Route::get('/usuarios/{id}', 'UserAdminController@show')->name('usuarios.show');
        });
        Route::group(array('middleware' => 'can:create,App\\Models\\Usuario'), function () {
            Route::post('/usuarios', 'UserAdminController@store')->name('usuarios.store');
        });
        Route::group(array('middleware' => 'can:update,App\\Models\\Usuario'), function () {
            Route::match(['put', 'patch'], '/usuarios/{id}', 'UserAdminController@update')->name('usuarios.update');
        });
        Route::group(array('middleware' => 'can:delete,App\\Models\\Usuario'), function () {
            Route::delete('/usuarios/{id}', 'UserAdminController@destroy')->name('usuarios.destroy');
        });

        Route::group(array('middleware' => 'can:viewAny,App\\Models\\Area'), function () {
            Route::get('/setores/{id}', 'SectorAdminController@show')->name('setores.show');
        });
        Route::group(array('middleware' => 'can:create,App\\Models\\Area'), function () {
            Route::post('/setores', 'SectorAdminController@store')->name('setores.store');
        });
        Route::group(array('middleware' => 'can:update,App\\Models\\Area'), function () {
            Route::match(['put', 'patch'], '/setores/{id}', 'SectorAdminController@update')->name('setores.update');
        });
        Route::group(array('middleware' => 'can:delete,App\\Models\\Area'), function () {
            Route::delete('/setores/{id}', 'SectorAdminController@destroy')->name('setores.destroy');
        });

        Route::group(array('middleware' => 'can:viewAny,App\\Models\\Banco'), function () {
            Route::get('/clientes/{id}', 'ClientAdminController@show')->name('clientes.show');
        });
        Route::group(array('middleware' => 'can:create,App\\Models\\Banco'), function () {
            Route::post('/clientes', 'ClientAdminController@store')->name('clientes.store');
        });
        Route::group(array('middleware' => 'can:update,App\\Models\\Banco'), function () {
            Route::match(['put', 'patch'], '/clientes/{id}', 'ClientAdminController@update')->name('clientes.update');
        });
        Route::group(array('middleware' => 'can:delete,App\\Models\\Banco'), function () {
            Route::delete('/clientes/{id}', 'ClientAdminController@destroy')->name('clientes.destroy');
        });

        Route::group(array('middleware' => 'can:viewAny,App\\Models\\Andamento'), function () {
            Route::get('/andamentos/{id}', 'AndamentoAdminController@show')->name('andamentos.show');
        });
        Route::group(array('middleware' => 'can:create,App\\Models\\Andamento'), function () {
            Route::post('/andamentos', 'AndamentoAdminController@store')->name('andamentos.store');
        });
        Route::group(array('middleware' => 'can:update,App\\Models\\Andamento'), function () {
            Route::match(['put', 'patch'], '/andamentos/{id}', 'AndamentoAdminController@update')->name('andamentos.update');
        });
        Route::group(array('middleware' => 'can:delete,App\\Models\\Andamento'), function () {
            Route::delete('/andamentos/{id}', 'AndamentoAdminController@destroy')->name('andamentos.destroy');
        });

        Route::group(array('middleware' => 'can:viewAny,App\\Models\\Semana'), function () {
            Route::get('/semanas/{id}', 'WeekController@show')->name('semanas.show');
        });
        Route::group(array('middleware' => 'can:create,App\\Models\\Semana'), function () {
            Route::post('/semanas', 'WeekController@store')->name('semanas.store');
        });
        Route::group(array('middleware' => 'can:update,App\\Models\\Semana'), function () {
            Route::match(['put', 'patch'], '/semanas/{id}', 'WeekController@update')->name('semanas.update');
        });
        Route::group(array('middleware' => 'can:delete,App\\Models\\Semana'), function () {
            Route::delete('/semanas/{id}', 'WeekController@destroy')->name('semanas.destroy');
        });

        Route::group(array('middleware' => 'can:viewAny,App\\Models\\Regiao'), function () {
            Route::get('/regioes/{id}', 'RegionAdminController@show')->name('regioes.show');
        });
        Route::group(array('middleware' => 'can:create,App\\Models\\Regiao'), function () {
            Route::post('/regioes', 'RegionAdminController@store')->name('regioes.store');
        });
        Route::group(array('middleware' => 'can:update,App\\Models\\Regiao'), function () {
            Route::match(['put', 'patch'], '/regioes/{id}', 'RegionAdminController@update')->name('regioes.update');
        });
        Route::group(array('middleware' => 'can:delete,App\\Models\\Regiao'), function () {
            Route::delete('/regioes/{id}', 'RegionAdminController@destroy')->name('regioes.destroy');
        });

        Route::group(array('middleware' => 'can:viewAny,App\\Models\\MetaAndamento'), function () {
            Route::get('/metas/{id}', 'MetaController@show')->name('metas.show');
        });
        Route::group(array('middleware' => 'can:create,App\\Models\\MetaAndamento'), function () {
            Route::post('/metas', 'MetaController@store')->name('metas.store');
        });
        Route::group(array('middleware' => 'can:update,App\\Models\\MetaAndamento'), function () {
            Route::match(['put', 'patch'], '/metas/{id}', 'MetaController@update')->name('metas.update');
        });
        Route::group(array('middleware' => 'can:delete,App\\Models\\MetaAndamento'), function () {
            Route::delete('/metas/{id}', 'MetaController@destroy')->name('metas.destroy');
        });
    });
});

Route::get('/health', 'HomeController@health');

if (app()->environment('testing')) {
    Route::middleware(array('web', 'auth', 'can:viewAny,App\\Models\\Usuario'))
        ->get('/_test/http/usuarios-protegido', function () {
            return response('ok', 200);
        })->name('test.http.usuarios');

    Route::middleware(array('web', 'auth', 'can:access-***REMOVED***'))
        ->get('/_test/http/***REMOVED***-protegido', function () {
            return response('ok', 200);
        })->name('test.http.***REMOVED***');

    Route::middleware(array('web', 'auth', 'can:viewAny,App\\Models\\MetaAndamento'))
        ->get('/_test/http/metas-protegido', function () {
            return response('ok', 200);
        })->name('test.http.metas');
}
