<?php

namespace Progmix\Donations\Providers;

use Juzaweb\CMS\Facades\ActionRegister;
use Juzaweb\CMS\Support\ServiceProvider;
use Progmix\Donations\Actions\DonationsAction;

class DonationsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        ActionRegister::register([DonationsAction::class]);
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
