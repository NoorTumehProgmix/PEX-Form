<?php


use Juzaweb\API\Http\Controllers\Admin\DataTableController;

Route::group(
    [
        'prefix' => 'datatable',
    ],
    function () {
        Route::get('/{id}', [DataTableController::class, 'show']);
        //Route::get('/{id}/data', [DataTableController::class, 'getData']);
    }
);
