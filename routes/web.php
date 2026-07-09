<?php

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
});

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'app' => config('app.name'),
        'framework' => app()->version(),
    ]);
});
