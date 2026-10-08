<?php

use Progmix\SearchLog\Http\Controllers\SearchLogController;
use Progmix\SearchLog\Http\Controllers\QuickLinksController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register Admin routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "admin" middleware group. Now create something great!
|
*/
Route::jwResource('search-log', SearchLogController::class);
Route::jwResource('quick-links', QuickLinksController::class);
Route::get('search-log/export', [SearchLogController::class, 'exportTable'])->name('search-log.export');
