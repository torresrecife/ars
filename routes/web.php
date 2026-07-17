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
    Route::post('/ajax/usuarios', 'UserAdminController@webAjax')->name('ajax.usuarios');
    Route::post('/ajax/setores', 'SectorAdminController@webAjax')->name('ajax.setores');
    Route::post('/ajax/clientes', 'ClientAdminController@webAjax')->name('ajax.clientes');
    Route::post('/ajax/andamentos', 'AndamentoAdminController@webAjax')->name('ajax.andamentos');
    Route::post('/ajax/metas', 'MetaController@webAjax')->name('ajax.metas');
    Route::post('/ajax/semanas', 'WeekController@webAjax')->name('ajax.semanas');
    Route::match(['get', 'post'], '/detalhes/andamentos', 'AndamentoDetailController@webIndex')->name('detalhes.andamentos');
    Route::match(['get', 'post'], '/detalhes/faturamento', 'FinancialDetailController@webIndex')->name('detalhes.faturamento');

    Route::get('/carteiras', 'HomeController@webCarteiras')->name('carteiras');
    Route::get('/painel', 'HomeController@webPainel')->name('painel');
    Route::get('/producao', 'HomeController@webProducao')->name('producao');
    Route::get('/relatorio', 'HomeController@webRelatorio')->name('relatorio');
    Route::get('/***REMOVED***', 'HomeController@webAdmin')->name('***REMOVED***');
    Route::get('/usuarios', 'HomeController@webUsuarios')->name('usuarios');
    Route::get('/setores', 'HomeController@webSetores')->name('setores');
    Route::get('/clientes', 'HomeController@webClientes')->name('clientes');
    Route::get('/andamentos', 'HomeController@webAndamentos')->name('andamentos');
    Route::get('/metas', 'HomeController@webMetas')->name('metas');
    Route::get('/semanas', 'HomeController@webSemanas')->name('semanas');
    Route::get('/regioes', 'HomeController@webRegioes')->name('regioes');

    Route::post('/ajax/regioes', 'RegionAdminController@webAjax')->name('ajax.regioes');
});

Route::get('/health', 'HomeController@health');
