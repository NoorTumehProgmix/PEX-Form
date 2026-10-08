<?php


use Juzaweb\API\Http\Controllers\Admin\SettingController;

Route::group(
    [
        'prefix' => 'setting',
    ],
    function () {
        Route::get('configs', [SettingController::class, 'configs']);
    }
);
