<?php

/**
 * JUZAWEB CMS - Laravel CMS for Your Project
 *
 * @package    juzaweb/juzacms
 * @author     The Anh Dang
 * @link       https://github.com/juzaweb/juzacms
 * @license    GNU V2
 */

use Juzaweb\Backend\Http\Controllers\Backend\CommentController;
use Juzaweb\Backend\Http\Controllers\Backend\TaxonomyController;
use Juzaweb\Backend\Http\Controllers\Backend\PostController;
use Juzaweb\Backend\Http\Datatables\PostTypeDataTable;
use Illuminate\Support\Facades\Route;


Route::jwResource(
    'post-type/{type}/comments',
    CommentController::class,
    [
        'name' => 'comments'
    ]
);

Route::jwResource(
    'taxonomy/{type}/{taxonomy}',
    TaxonomyController::class,
    [
        'name' => 'taxonomies'
    ]
);

Route::get(
    'taxonomy/{type}/{taxonomy}/component-item',
    [TaxonomyController::class, 'getTagComponent']
);

Route::jwResource(
    'post-type/{type}',
    PostController::class,
    [
        'name' => 'posts'
    ]
);

Route::post('post-type/{type}/import', [PostController::class, 'import'])->name('post.import');
Route::get('post-type/{type}/export', [PostController::class, 'export'])->name('post.export');
Route::get('post-type/{type}/export', [PostController::class, 'export'])->name('post.export');

Route::post(
    '/datatable/bulk-actions/working-hours',
    [PostTypeDataTable::class, 'bulkActionsWithForm']
)->name('admin.posts.bulk_action.working.hours');

