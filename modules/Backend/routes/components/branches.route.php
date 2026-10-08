<?php

use Juzaweb\API\Http\Controllers\Admin\SettingController;
use Juzaweb\Backend\Http\Controllers\Backend\BranchController;
use Juzaweb\Backend\Http\Controllers\Backend\SettingItemController;


Route::jwResource('branches-working-hours', BranchController::class);

Route::jwResource('branches-setting', SettingItemController::class);
