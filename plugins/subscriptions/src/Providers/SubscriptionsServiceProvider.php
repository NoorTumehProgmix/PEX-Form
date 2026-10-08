<?php

namespace Juzaweb\Subscriptions\Providers;
use Juzaweb\CMS\Facades\ActionRegister;

use Juzaweb\CMS\Support\ServiceProvider;
use Juzaweb\Subscriptions\Actions\SubscriptionsAction;

class SubscriptionsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        ActionRegister::register([SubscriptionsAction::class]);
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
