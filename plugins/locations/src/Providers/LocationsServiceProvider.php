<?php

namespace Progmix\Locations\Providers;

use Juzaweb\CMS\Support\ServiceProvider;
use Progmix\Locations\Actions\LocationAction;
use Juzaweb\CMS\Facades\ActionRegister;

class LocationsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        ActionRegister::register(LocationAction::class);
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
