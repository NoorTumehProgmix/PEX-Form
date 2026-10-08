<?php

use Progmix\Locations\Http\Controllers\CountryController;
use Progmix\Locations\Http\Controllers\StateController;
use Progmix\Locations\Http\Controllers\CityController;


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

Route::jwResource('locations/countries', CountryController::class);
Route::jwResource('locations/states', StateController::class);
Route::jwResource('locations/cities', CityController::class);
