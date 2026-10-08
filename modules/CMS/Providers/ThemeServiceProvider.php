<?php


namespace Juzaweb\CMS\Providers;

use Illuminate\Support\Facades\Lang;
use Juzaweb\CMS\Contracts\ThemeLoaderContract;
use Juzaweb\CMS\Contracts\LocalThemeRepositoryContract;
use Juzaweb\CMS\Facades\ActionRegister;
use Juzaweb\CMS\Facades\ThemeLoader;
use Juzaweb\CMS\Support\ServiceProvider;
use Juzaweb\CMS\Support\Theme\Theme;
use Juzaweb\CMS\Support\LocalThemeRepository;
use Juzaweb\Frontend\Actions\FrontendAction;
use Juzaweb\Frontend\Actions\ThemeAction;

class ThemeServiceProvider extends ServiceProvider
{


    public function register()
    {
        $this->app->singleton(
            ThemeLoaderContract::class,
            function ($app) {
                return new Theme($app, $app['view']->getFinder(), $app['config'], $app['translator']);
            }
        );

        $this->app->singleton(
            LocalThemeRepositoryContract::class,
            function ($app) {
                $path = config('juzaweb.theme.path');
                return new LocalThemeRepository($app, $path);
            }
        );

        $this->app->alias(LocalThemeRepositoryContract::class, 'themes');
    }
}
