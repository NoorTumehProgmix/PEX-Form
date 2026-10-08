<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


// Retrieve all APIs from the database

use Progmix\Api\Http\Controllers\ApiHandlerController;

Route::match(['get', 'post', 'put', 'delete'], '/{any}',  [ApiHandlerController::class, 'index'])->where('any', '.*')->middleware('CalculateRequestDuration');
