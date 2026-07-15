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
    Route::match(['get', 'post'], '/index', 'HomeController@webIndex')->name('legacy.home');
    Route::match(['get', 'post'], '/index.php', 'HomeController@webIndex');
    Route::match(['get', 'post'], '/logout', 'AuthController@logout')->name('logout');

    Route::match(['get', 'post'], '/carteiras', function (Request $request) {
        return app('App\Http\Controllers\HomeController')->webSectionPage($request, 'carteiras');
    })->name('carteiras');

    Route::match(['get', 'post'], '/painel', function (Request $request) {
        return app('App\Http\Controllers\HomeController')->webSectionPage($request, 'painel');
    })->name('painel');

    Route::match(['get', 'post'], '/producao', function (Request $request) {
        return app('App\Http\Controllers\HomeController')->webSectionPage($request, 'producao');
    })->name('producao');

    Route::match(['get', 'post'], '/***REMOVED***', function (Request $request) {
        return app('App\Http\Controllers\HomeController')->webSectionPage($request, '***REMOVED***');
    })->name('***REMOVED***');

    Route::match(['get', 'post'], '/usuarios', function (Request $request) {
        return app('App\Http\Controllers\HomeController')->webSectionPage($request, 'usuarios');
    })->name('usuarios');

    Route::match(['get', 'post'], '/setores', function (Request $request) {
        return app('App\Http\Controllers\HomeController')->webSectionPage($request, 'setores');
    })->name('setores');

    Route::match(['get', 'post'], '/clientes', function (Request $request) {
        return app('App\Http\Controllers\HomeController')->webSectionPage($request, 'clientes');
    })->name('clientes');

    Route::match(['get', 'post'], '/andamentos', function (Request $request) {
        return app('App\Http\Controllers\HomeController')->webSectionPage($request, 'andamentos');
    })->name('andamentos');

    Route::match(['get', 'post'], '/metas', function (Request $request) {
        $input = $request->all();
        $hasMetaContext = isset($input['startBanco']) || isset($input['banco_id']) || isset($input['meta_mes']) || isset($input['meta_ano']);
        return app('App\Http\Controllers\HomeController')->webSectionPage($request, $hasMetaContext ? 'metas-***REMOVED***' : 'metas-select');
    })->name('metas');

    Route::match(['get', 'post'], '/semanas', function (Request $request) {
        return app('App\Http\Controllers\HomeController')->webSectionPage($request, 'semanas');
    })->name('semanas');

    Route::match(['get', 'post'], '/regioes', function (Request $request) {
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
