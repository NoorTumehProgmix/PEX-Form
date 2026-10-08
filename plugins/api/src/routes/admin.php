<?php

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

use Progmix\Api\Http\Controllers\ApiController;
use Progmix\Api\Http\Controllers\ApiLogController;

Route::jwResource('api', 'Progmix\Api\Http\Controllers\ApiController');
Route::jwResource('log-api', 'Progmix\Api\Http\Controllers\ApiLogController');

Route::get('api', [ApiController::class, 'index'])->name('api.index');

Route::get('api/create', [ApiController::class, 'create'])->name('api.create');
Route::post('api/create', [ApiController::class, 'store']);

Route::get('api/edit/{api}', [ApiController::class, 'edit'])->name('api.edit');
Route::post('api/edit/{api}', [ApiController::class, 'update'])->name('api.update');

Route::get('api-Logs/{apiLog}', [ApiLogController::class, 'view'])->name('apiLogs.view');
