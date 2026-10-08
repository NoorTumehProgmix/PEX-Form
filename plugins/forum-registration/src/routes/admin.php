<?php

use Illuminate\Support\Facades\Route;

Route::get('/forum-registrations/export', 'Progmix\ForumRegistration\Http\Controllers\ForumRegistrationController@export')->name('admin.forum-registrations.export');
Route::jwResource('/forum-registrations', 'Progmix\ForumRegistration\Http\Controllers\ForumRegistrationController');
