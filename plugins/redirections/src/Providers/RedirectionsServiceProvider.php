<?php

namespace Progmix\Redirections\Providers;

use Juzaweb\CMS\Support\ServiceProvider;
use Progmix\Redirections\Actions\RedirectionsAction;
use Juzaweb\CMS\Facades\ActionRegister;

class RedirectionsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        ActionRegister::register(RedirectionsAction::class);
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
