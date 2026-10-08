<?php


use Juzaweb\API\Http\Controllers\PostController;
use Juzaweb\API\Http\Controllers\CommentController;

Route::group(
    [
        'prefix' => 'post-type',
    ],
    function () {
        Route::get('{type}', [PostController::class, 'index']);
        Route::get('{type}/{slug}', [PostController::class, 'show']);
        Route::get('{type}/{slug}/comments', [CommentController::class, 'index']);
        Route::post('{type}/{slug}/comments', [CommentController::class, 'store']);
    }
);
