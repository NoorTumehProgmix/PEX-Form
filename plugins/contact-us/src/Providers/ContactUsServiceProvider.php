<?php

namespace Progmix\ContactUs\Providers;

use Juzaweb\CMS\Facades\ActionRegister;
use Juzaweb\CMS\Support\ServiceProvider;
use Progmix\ContactUs\Actions\ContactUsAction;

class ContactUsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        ActionRegister::register([ContactUsAction::class]);
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
