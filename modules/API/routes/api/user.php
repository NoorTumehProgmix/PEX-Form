<?php


use Juzaweb\Api\Http\Controllers\UserController;

Route::group(
    [
        'prefix' => 'profile',
        'middleware' => 'auth:api',
    ],
    function () {
        Route::get('/', [UserController::class, 'profile']);
    }
);