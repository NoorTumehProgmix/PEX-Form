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

use Progmix\FormBuilder\Http\Controllers\FormBuilderController;
use Progmix\FormBuilder\Http\Controllers\FormSubmissionController;

// FormBuilderController
Route::jwResource('form-builder', 'Progmix\FormBuilder\Http\Controllers\FormBuilderController');

Route::get('form-builder/create/{type?}', [FormBuilderController::class, 'create'])->name('create.form');
Route::post('form-builders/store/{type?}', [FormBuilderController::class, 'store'])->name('form.store');

Route::get('form-builders/edit/{id}',  [FormBuilderController::class, 'edit'])->name('form.edit');
Route::post('form-builders/update/{form}', [FormBuilderController::class, 'update'])->name('form.update');

// FormSubmissionController
Route::jwResource('form-submissions', 'Progmix\FormBuilder\Http\Controllers\FormSubmissionController');
Route::get('form-submissions/show/{id}',  [FormSubmissionController::class, 'view'])->name('form.view.disabled');
Route::get('form-submissions/export', [FormSubmissionController::class, 'exportTable'])->name('form-submissions.export');
