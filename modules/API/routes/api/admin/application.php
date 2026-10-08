<?php

use Juzaweb\API\Http\Controllers\ApplicationController;

Route::group(
    [
        'prefix' => 'subscriptions',
        // 'middleware' => 'auth:api',

    ],
    function () {
        Route::get('/', [ApplicationController::class, 'index']);
    }
);
