<?php

namespace Progmix\Restrictions\Providers;

use Juzaweb\CMS\Support\ServiceProvider;
use Progmix\Restrictions\Actions\RestrictionsAction;
use Juzaweb\CMS\Facades\ActionRegister;

class RestrictionsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        ActionRegister::register(RestrictionsAction::class);
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
