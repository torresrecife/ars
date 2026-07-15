<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('legacy.guest')->group(function () {
    Route::get('/', 'AuthController@showLogin');
    Route::get('/login.php', 'AuthController@showLogin');
    Route::post('/login.php', 'AuthController@login');
    Route::get('/login', 'AuthController@showLogin')->name('login');
    Route::post('/login', 'AuthController@login')->name('login.submit');
});

Route::middleware('legacy.auth')->group(function () {
    Route::get('/index', 'HomeController@webIndex')->name('legacy.home');
    Route::get('/index.php', 'HomeController@webIndex');
    Route::get('/logout', 'AuthController@logout')->name('logout');

    Route::get('/carteiras', function (Request $request) {
        return app('App\Http\Controllers\HomeController')->webSectionPage($request, 'carteiras');
    })->name('carteiras');

    Route::get('/painel', function (Request $request) {
        return app('App\Http\Controllers\HomeController')->webSectionPage($request, 'painel');
    })->name('painel');

    Route::get('/producao', function (Request $request) {
        return app('App\Http\Controllers\HomeController')->webSectionPage($request, 'producao');
    })->name('producao');

    Route::get('/relatorio', function (Request $request) {
        $input = $request->all();
        return app('App\Http\Controllers\HomeController')->webSectionPage($request, isset($input['geral']) && (string) $input['geral'] === '1' ? 'relatorio-semanal' : 'relatorio-mensal');
    })->name('relatorio');

    Route::get('/***REMOVED***', function (Request $request) {
        return app('App\Http\Controllers\HomeController')->webSectionPage($request, '***REMOVED***');
    })->name('***REMOVED***');

    Route::get('/usuarios', function (Request $request) {
        return app('App\Http\Controllers\HomeController')->webSectionPage($request, 'usuarios');
    })->name('usuarios');

    Route::get('/setores', function (Request $request) {
        return app('App\Http\Controllers\HomeController')->webSectionPage($request, 'setores');
    })->name('setores');

    Route::get('/clientes', function (Request $request) {
        return app('App\Http\Controllers\HomeController')->webSectionPage($request, 'clientes');
    })->name('clientes');

    Route::get('/andamentos', function (Request $request) {
        return app('App\Http\Controllers\HomeController')->webSectionPage($request, 'andamentos');
    })->name('andamentos');

    Route::get('/metas', function (Request $request) {
        $input = $request->all();
        $hasMetaContext = isset($input['startBanco']) || isset($input['banco_id']) || isset($input['meta_mes']) || isset($input['meta_ano']);
        return app('App\Http\Controllers\HomeController')->webSectionPage($request, $hasMetaContext ? 'metas-***REMOVED***' : 'metas-select');
    })->name('metas');

    Route::get('/semanas', function (Request $request) {
        return app('App\Http\Controllers\HomeController')->webSectionPage($request, 'semanas');
    })->name('semanas');

    Route::get('/regioes', function (Request $request) {
        return app('App\Http\Controllers\HomeController')->webSectionPage($request, 'regioes');
    })->name('regioes');
});

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'app' => config('app.name'),
        'framework' => app()->version(),
    ]);
});
