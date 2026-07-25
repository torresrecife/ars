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
    Route::middleware('can:access-admin')->group(function () {
        Route::get('/admin', 'HomeController@webSectionPage')->defaults('section', 'admin')->name('admin');
        Route::get('/metas', 'HomeController@webMetas')->middleware('can:viewAny,App\\Models\\MetaAndamento')->name('metas');
    });
    Route::group(array('middleware' => 'can:viewAny,App\\Models\\MetaAndamento'), function () {
        Route::get('/metas/novo', 'MetaController@createPage')->name('metas.create');
        Route::get('/metas/{id}/editar', 'MetaController@editPage')->name('metas.edit');
    });
    Route::group(array('middleware' => 'can:create,App\\Models\\MetaAndamento'), function () {
        Route::post('/metas', 'MetaController@storePage')->name('metas.store.page');
    });
    Route::group(array('middleware' => 'can:update,App\\Models\\MetaAndamento'), function () {
        Route::match(['put', 'patch'], '/metas/{id}', 'MetaController@updatePage')->name('metas.update.page');
        Route::post('/metas/reordenar', 'MetaController@reorder')->name('metas.reorder.page');
    });
    Route::group(array('middleware' => 'can:delete,App\\Models\\MetaAndamento'), function () {
        Route::get('/metas/{id}/excluir', 'MetaController@confirmDeletePage')->name('metas.confirm-delete');
        Route::delete('/metas/{id}', 'MetaController@destroyPage')->name('metas.destroy.page');
    });

    Route::group(array('middleware' => 'can:viewAny,App\\Models\\Usuario'), function () {
        Route::get('/usuarios/novo', 'UserAdminController@createPage')->name('usuarios.create');
        Route::get('/usuarios/{id}/editar', 'UserAdminController@editPage')->name('usuarios.edit');
        Route::get('/usuarios', 'HomeController@webSectionPage')->defaults('section', 'usuarios')->name('usuarios');
    });
    Route::group(array('middleware' => 'can:create,App\\Models\\Usuario'), function () {
        Route::post('/usuarios', 'UserAdminController@storePage')->name('usuarios.store.page');
    });
    Route::group(array('middleware' => 'can:update,App\\Models\\Usuario'), function () {
        Route::match(['put', 'patch'], '/usuarios/{id}', 'UserAdminController@updatePage')->name('usuarios.update.page');
    });
    Route::group(array('middleware' => 'can:delete,App\\Models\\Usuario'), function () {
        Route::get('/usuarios/{id}/excluir', 'UserAdminController@confirmDeletePage')->name('usuarios.confirm-delete');
        Route::delete('/usuarios/{id}', 'UserAdminController@destroyPage')->name('usuarios.destroy.page');
    });
    Route::group(array('middleware' => 'can:viewAny,App\\Models\\Area'), function () {
        Route::get('/setores/novo', 'SectorAdminController@createPage')->name('setores.create');
        Route::get('/setores/{id}/editar', 'SectorAdminController@editPage')->name('setores.edit');
        Route::get('/setores', 'HomeController@webSectionPage')->defaults('section', 'setores')->name('setores');
    });
    Route::group(array('middleware' => 'can:create,App\\Models\\Area'), function () {
        Route::post('/setores', 'SectorAdminController@storePage')->name('setores.store.page');
    });
    Route::group(array('middleware' => 'can:update,App\\Models\\Area'), function () {
        Route::match(['put', 'patch'], '/setores/{id}', 'SectorAdminController@updatePage')->name('setores.update.page');
    });
    Route::group(array('middleware' => 'can:delete,App\\Models\\Area'), function () {
        Route::get('/setores/{id}/excluir', 'SectorAdminController@confirmDeletePage')->name('setores.confirm-delete');
        Route::delete('/setores/{id}', 'SectorAdminController@destroyPage')->name('setores.destroy.page');
    });
    Route::group(array('middleware' => 'can:viewAny,App\\Models\\Banco'), function () {
        Route::get('/clientes/novo', 'ClientAdminController@createPage')->name('clientes.create');
        Route::get('/clientes/{id}/editar', 'ClientAdminController@editPage')->name('clientes.edit');
        Route::get('/clientes', 'HomeController@webSectionPage')->defaults('section', 'clientes')->name('clientes');
    });
    Route::group(array('middleware' => 'can:create,App\\Models\\Banco'), function () {
        Route::post('/clientes', 'ClientAdminController@storePage')->name('clientes.store.page');
    });
    Route::group(array('middleware' => 'can:update,App\\Models\\Banco'), function () {
        Route::match(['put', 'patch'], '/clientes/{id}', 'ClientAdminController@updatePage')->name('clientes.update.page');
    });
    Route::group(array('middleware' => 'can:delete,App\\Models\\Banco'), function () {
        Route::get('/clientes/{id}/excluir', 'ClientAdminController@confirmDeletePage')->name('clientes.confirm-delete');
        Route::delete('/clientes/{id}', 'ClientAdminController@destroyPage')->name('clientes.destroy.page');
    });
    Route::group(array('middleware' => 'can:viewAny,App\\Models\\Andamento'), function () {
        Route::get('/andamentos/novo', 'AndamentoAdminController@createPage')->name('andamentos.create');
        Route::get('/andamentos/{id}/editar', 'AndamentoAdminController@editPage')->name('andamentos.edit');
        Route::get('/andamentos', 'HomeController@webSectionPage')->defaults('section', 'andamentos')->name('andamentos');
    });
    Route::group(array('middleware' => 'can:create,App\\Models\\Andamento'), function () {
        Route::post('/andamentos', 'AndamentoAdminController@storePage')->name('andamentos.store');
    });
    Route::group(array('middleware' => 'can:update,App\\Models\\Andamento'), function () {
        Route::match(['put', 'patch'], '/andamentos/{id}', 'AndamentoAdminController@updatePage')->name('andamentos.update');
    });
    Route::group(array('middleware' => 'can:delete,App\\Models\\Andamento'), function () {
        Route::get('/andamentos/{id}/excluir', 'AndamentoAdminController@confirmDeletePage')->name('andamentos.confirm-delete');
        Route::delete('/andamentos/{id}', 'AndamentoAdminController@destroyPage')->name('andamentos.destroy');
    });
    Route::group(array('middleware' => 'can:viewAny,App\\Models\\Semana'), function () {
        Route::get('/semanas/novo', 'WeekController@createPage')->name('semanas.create');
        Route::get('/semanas/{id}/editar', 'WeekController@editPage')->name('semanas.edit');
        Route::get('/semanas', 'HomeController@webSectionPage')->defaults('section', 'semanas')->name('semanas');
    });
    Route::group(array('middleware' => 'can:create,App\\Models\\Semana'), function () {
        Route::post('/semanas', 'WeekController@storePage')->name('semanas.store.page');
    });
    Route::group(array('middleware' => 'can:update,App\\Models\\Semana'), function () {
        Route::match(['put', 'patch'], '/semanas/{id}', 'WeekController@updatePage')->name('semanas.update.page');
    });
    Route::group(array('middleware' => 'can:delete,App\\Models\\Semana'), function () {
        Route::get('/semanas/{id}/excluir', 'WeekController@confirmDeletePage')->name('semanas.confirm-delete');
        Route::delete('/semanas/{id}', 'WeekController@destroyPage')->name('semanas.destroy.page');
    });
    Route::group(array('middleware' => 'can:viewAny,App\\Models\\Regiao'), function () {
        Route::get('/regioes/novo', 'RegionAdminController@createPage')->name('regioes.create');
        Route::get('/regioes/{id}/editar', 'RegionAdminController@editPage')->name('regioes.edit');
        Route::get('/regioes', 'HomeController@webSectionPage')->defaults('section', 'regioes')->name('regioes');
    });
    Route::group(array('middleware' => 'can:create,App\\Models\\Regiao'), function () {
        Route::post('/regioes', 'RegionAdminController@storePage')->name('regioes.store.page');
    });
    Route::group(array('middleware' => 'can:update,App\\Models\\Regiao'), function () {
        Route::match(['put', 'patch'], '/regioes/{id}', 'RegionAdminController@updatePage')->name('regioes.update.page');
    });
    Route::group(array('middleware' => 'can:delete,App\\Models\\Regiao'), function () {
        Route::get('/regioes/{id}/excluir', 'RegionAdminController@confirmDeletePage')->name('regioes.confirm-delete');
        Route::delete('/regioes/{id}', 'RegionAdminController@destroyPage')->name('regioes.destroy.page');
    });

    Route::prefix('admin')->as('admin.')->group(function () {
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
            Route::post('/metas/reordenar', 'MetaController@reorder')->name('metas.reorder');
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

    Route::middleware(array('web', 'auth', 'can:access-admin'))
        ->get('/_test/http/admin-protegido', function () {
            return response('ok', 200);
        })->name('test.http.admin');

    Route::middleware(array('web', 'auth', 'can:viewAny,App\\Models\\MetaAndamento'))
        ->get('/_test/http/metas-protegido', function () {
            return response('ok', 200);
        })->name('test.http.metas');
}
