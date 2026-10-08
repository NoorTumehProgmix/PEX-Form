<?php


use Juzaweb\CMS\Support\Route\Auth;
use Juzaweb\Backend\Http\Controllers\Auth\LoginController;

Route::group(
    ['middleware' => 'auth'],
    function () {
        Route::post(
            'logout',
            [LoginController::class, 'logout']
        )
        ->name('logout');
    }
);

Route::group(
    [
        'middleware' => ['guest','csp'],
        'as' => 'admin.',
        'prefix' => config('juzaweb.admin_prefix'),
        'namespace' => 'Juzaweb\CMS\Http\Controllers',
    ],
    function () {
        Auth::routes();
    }
);
