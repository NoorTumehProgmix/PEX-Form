<?php

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Schema;

return [
    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
     */

    'name' => env('APP_NAME', 'CMS'),
    'license_key'     => env('LICENSE_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
     */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
     */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
     */

    'url' => env('APP_URL', 'http://localhost'),

    'frontend_url' => env('FRONTEND_URL', 'http://localhost:3000'),

    'asset_url' => env('ASSET_URL'),

    'proxy_url' => env('PROXY_URL'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
     */

    'timezone' => 'Asia/Jerusalem',

    /*
    |--------------------------------------------------------------------------
    | Application Meta
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default Metas for your application. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
     */

    'metas' => [
        "meta_title" => env('APP_NAME', 'PROGMIX'),
        "meta_heading" =>  env('APP_NAME', 'PROGMIX'),
        "meta_author" =>  env('APP_NAME', 'PROGMIX'),
        "meta_keywords" =>  env('APP_NAME', 'PROGMIX'),
        "meta_title_keywords" =>  env('APP_NAME', 'PROGMIX'),
        "meta_canonical" => env('APP_URL', 'http://localhost:8000'),
        "meta_showRobots" => 'yes',
        "meta_robots" => '',
        "meta_description" => 'Description',
        "meta_copyright" =>  env('APP_NAME', 'PROGMIX'),
        //facebook
        "meta_og_site_name" =>  env('APP_NAME', 'PROGMIX'),
        "meta_og_title" =>  env('APP_NAME', 'PROGMIX'),
        "meta_og_type" => 'website',
        "meta_og_url" => 'meta_og_url',
        "meta_og_image" => 'meta_og_image',
        //twitter
        "meta_twitter_card" => 'summary_large_image',
        "meta_twitter_title" =>  env('APP_NAME', 'PROGMIX'),
        "meta_twitter_description" => null,
        "meta_twitter_image" => 'Meta Twitter Image',
        "meta_twitter_image_alt" => '',
    ],

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
     */

    'locale' => 'ar',
    'dashboard_locale' => 'ar',
    'locales' => [
        'ar' => [
            'name' => 'عربي',
            'key' => 'AR',
            'dir' => 'rtl',
        ],
    ],
    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
     */

    'fallback_locale' => 'ar',

    /*
    |--------------------------------------------------------------------------
    | Faker Locale
    |--------------------------------------------------------------------------
    |
    | This locale will be used by the Faker PHP library when generating fake
    | data for your database seeds. For example, this will be used to get
    | localized telephone numbers, street address information and more.
    |
     */

    'faker_locale' => 'ar_SA',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
     */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
     */

    'providers' => [
        ...\Juzaweb\CMS\Facades\Facades::defaultServiceProviders(),
        Barryvdh\Debugbar\ServiceProvider::class,
        Maatwebsite\Excel\ExcelServiceProvider::class,

        /*
     * Application Service Providers...
     */
    ],

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
     */

    'aliases' => Facade::defaultAliases()->merge(
        [
            'Excel' => Maatwebsite\Excel\Facades\Excel::class,
        ]
    )->toArray(),

];
