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
        return app('App\Http\Controllers\HomeController')->webStatePage($request, 1);
    })->name('carteiras');

    Route::match(['get', 'post'], '/painel', function (Request $request) {
        return app('App\Http\Controllers\HomeController')->webStatePage($request, 2);
    })->name('painel');

    Route::match(['get', 'post'], '/producao', function (Request $request) {
        return app('App\Http\Controllers\HomeController')->webStatePage($request, 3);
    })->name('producao');

    Route::match(['get', 'post'], '/admin', function (Request $request) {
        return app('App\Http\Controllers\HomeController')->webStatePage($request, 5);
    })->name('admin');

    Route::match(['get', 'post'], '/usuarios', function (Request $request) {
        return app('App\Http\Controllers\HomeController')->webStatePage($request, 8);
    })->name('usuarios');

    Route::match(['get', 'post'], '/setores', function (Request $request) {
        return app('App\Http\Controllers\HomeController')->webStatePage($request, 9);
    })->name('setores');

    Route::match(['get', 'post'], '/clientes', function (Request $request) {
        return app('App\Http\Controllers\HomeController')->webStatePage($request, 11);
    })->name('clientes');

    Route::match(['get', 'post'], '/andamentos', function (Request $request) {
        return app('App\Http\Controllers\HomeController')->webStatePage($request, 12);
    })->name('andamentos');

    Route::match(['get', 'post'], '/metas', function (Request $request) {
        $input = $request->all();
        $hasMetaContext = isset($input['startBanco']) || isset($input['banco_id']) || isset($input['meta_mes']) || isset($input['meta_ano']);
        return app('App\Http\Controllers\HomeController')->webStatePage($request, $hasMetaContext ? 14 : 13);
    })->name('metas');

    Route::match(['get', 'post'], '/semanas', function (Request $request) {
        return app('App\Http\Controllers\HomeController')->webStatePage($request, 15);
    })->name('semanas');

    Route::match(['get', 'post'], '/regioes', function (Request $request) {
        return app('App\Http\Controllers\HomeController')->webStatePage($request, 16);
    })->name('regioes');
});

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'app' => config('app.name'),
        'framework' => app()->version(),
    ]);
});
