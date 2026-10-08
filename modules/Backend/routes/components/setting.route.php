<?php

use Juzaweb\Backend\Http\Controllers\Backend\Setting\MediaController;
use Juzaweb\Backend\Http\Controllers\Backend\Setting\SystemSettingController;

Route::group(
    ['prefix' => 'setting'],
    function () {
        Route::get('/{page}', [SystemSettingController::class, 'index'])->name('admin.setting')
            ->defaults('page', 'system');
        Route::get('/{page}/{form}', [SystemSettingController::class, 'index'])->name('admin.setting.form')
            ->defaults('page', 'system');
        Route::post('/save', [SystemSettingController::class, 'save'])->name('admin.setting.save');
    }
);

Route::get('/options-media', [MediaController::class, 'index'])->name('admin.setting.media');
Route::post('/dispatch-jobs', [MediaController::class, 'dispatchJobs'])->name('admin.setting.media.dispatch-jobs');
Route::post('/fetch-resize', [MediaController::class, 'fetchPosts'])->name('admin.setting.media.fetch-resize');
