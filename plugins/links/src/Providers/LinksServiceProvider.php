<?php

namespace Progmix\Links\Providers;

use Juzaweb\CMS\Support\ServiceProvider;
use Progmix\Links\Actions\LinksAction;
use Juzaweb\CMS\Facades\ActionRegister;

class LinksServiceProvider extends ServiceProvider
{
    public function boot()
    {
        ActionRegister::register(LinksAction::class);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
