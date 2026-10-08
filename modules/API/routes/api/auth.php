<?php


use Juzaweb\API\Http\Controllers\Auth\LoginController;
use Juzaweb\API\Http\Controllers\Auth\RegisterController;

Route::group(
    ['prefix' => 'auth'],
    function () {
        Route::post('login', [LoginController::class, 'login']);
        Route::post('register', [RegisterController::class, 'register']);
    }
);

Route::group(
    [
        'prefix' => 'auth',
        'middleware' => 'auth:api',
    ],
    function () {
        Route::post('logout', [LoginController::class, 'logout']);

        Route::get('profile', [LoginController::class, 'profile']);
    }
);
