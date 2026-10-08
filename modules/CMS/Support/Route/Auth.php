<?php



namespace Juzaweb\CMS\Support\Route;

use Illuminate\Support\Facades\Route;
use Juzaweb\Backend\Http\Controllers\Auth\LoginController;
use Juzaweb\Backend\Http\Controllers\Auth\RegisterController;
use Juzaweb\Backend\Http\Controllers\Auth\ForgotPasswordController;
use Juzaweb\Backend\Http\Controllers\Auth\ResetPasswordController;
use Juzaweb\Backend\Http\Controllers\Auth\SocialLoginController;

class Auth
{
    public static function routes(): void
    {
        Route::group(
            [
                'middleware' => ['guest', 'XssSanitization', 'throttle'],
            ],
            function () {
                Route::get('login', [LoginController::class, 'index'])->name('login');
                Route::post('login', [LoginController::class, 'login']);

                // Route::get(
                //     'auth/{method}/redirect',
                //     [SocialLoginController::class, 'redirect']
                // )->name('auth.socialites.redirect');

                // Route::get(
                //     'auth/{method}/callback',
                //     [SocialLoginController::class, 'callback']
                // )->name('auth.socialites.callback');
            }
        );

        Route::group(
            ['middleware' => 'auth'],
            function () {
                Route::post(
                    'logout',
                    [LoginController::class, 'logout']
                )
                    ->name('logout');
            }
        );
    }
}
