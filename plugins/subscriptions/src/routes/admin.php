<?php
use Juzaweb\Subscriptions\Http\Controllers\Backend\SubscriptionController;
/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register Admin routes for your Subscription. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "admin" middleware group. Now create something great!
|
*/
Route::jwResource('subscriptions', 'Juzaweb\Subscriptions\Http\Controllers\Backend\SubscriptionController');
Route::get('subscriptions/export', [SubscriptionController::class, 'exportTable'])->name('subscriptions.export');
