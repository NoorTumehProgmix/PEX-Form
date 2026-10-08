<?php


use Juzaweb\API\Http\Controllers\Admin\UserController;

Route::group(
    [],
    function () {
        Route::apiResource('users', UserController::class)->names('admin.user');
    }
);
