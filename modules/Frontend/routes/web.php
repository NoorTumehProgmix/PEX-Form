<?php

use Juzaweb\CMS\Support\Route\Auth;
use Juzaweb\Frontend\Http\Controllers\FormController;
use Juzaweb\Frontend\Http\Controllers\PostController;
use Juzaweb\Frontend\Http\Controllers\SitemapController;
use Juzaweb\Frontend\Http\Controllers\SearchController;
use Juzaweb\Frontend\Http\Controllers\HomeController;
use Juzaweb\Frontend\Http\Controllers\ForumRegistrationController;
use Juzaweb\Frontend\Http\Controllers\TempIpCheckController;
use Progmix\Redirections\Models\Redirection;

//SITEMAP
Route::get('{lang}/sitemap.xml', [SitemapController::class, 'index'])
    ->where('lang', '[a-zA-Z]{2}')
    ->name('sitemap.index');

Route::group(['middleware' => ['security']], function () {
    Auth::routes();

    Route::middleware(['frontend'])->group(function () {
        Route::get('register', [ForumRegistrationController::class, 'show'])
            ->name('forum-registration.show');

        Route::post('forum-registration', [ForumRegistrationController::class, 'store'])
            ->middleware(['throttle:forum-registration'])
            ->name('forum-registration.store');

        // TEMP: delete after Cloudflare / real client IP is verified
        Route::get('temp-ip-check', [TempIpCheckController::class, 'show'])->name('temp-ip-check.show');
        Route::get('temp-ip-check/run', [TempIpCheckController::class, 'check'])->name('temp-ip-check.check');
    });

    Route::group([
        'prefix'     => '{locale?}',
        'where'      => ['locale' => '[a-zA-Z]{2}'],
        'middleware' => ['frontend'],
    ], function () {
        Route::match(['get', 'post'], 'search', [SearchController::class, 'index'])->name('search');


        Route::get('/', [PostController::class, 'home'])->name('home');

        Route::get('{slug?}', [PostController::class, 'post'])->name('post')->where('slug', '.+');
        // Route::post('save-form-submission', [FormController::class, 'saveFormJson'])->name('form.submission.save');
    });
    // Route::get('get-form/{form}', [FormController::class, 'getFormJson'])->name('get.form');

    //Handle redirection links without lang prefix
    if (pluginActive("progmix/redirections")) {
        Route::get('/{slug}', function ($slug) {
            $redirection = Cache::remember("redirection-$slug", get_config('cache_duration', 0), function () use ($slug) {
                return Redirection::where('old_link', "/$slug")->first();
            });
            if ($redirection) {
                return redirect($redirection->new_link, 301);
            }
            abort(404);
        })->where('slug', '.+');
    }
});
