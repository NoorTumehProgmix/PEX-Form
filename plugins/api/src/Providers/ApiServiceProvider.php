<?php

namespace Progmix\Api\Providers;

use Progmix\Api\Actions\ApiAction;
use Progmix\Api\Models\Api;
use Juzaweb\CMS\Support\ServiceProvider;
use Juzaweb\CMS\Facades\ActionRegister;

class ApiServiceProvider extends ServiceProvider
{
    public function boot()
    {
        ActionRegister::register(ApiAction::class);

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
