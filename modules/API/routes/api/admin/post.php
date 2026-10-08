<?php


use Juzaweb\API\Http\Controllers\Admin\PostController;

Route::group(
    [
        'prefix' => 'post-type',
    ],
    function () {
        Route::apiResource(
            '{type}',
            PostController::class,
            [
                'parameters' => [
                    '{type}' => 'id',
                ],
                'names' => 'post_type',
            ]
        );
    }
);
