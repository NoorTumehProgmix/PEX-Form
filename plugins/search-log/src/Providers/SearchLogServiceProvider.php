<?php

namespace Progmix\SearchLog\Providers;

use Juzaweb\CMS\Facades\ActionRegister;
use Juzaweb\CMS\Support\ServiceProvider;
use Progmix\SearchLog\Actions\SearchLogAction;

class SearchLogServiceProvider extends ServiceProvider
{
    public function boot()
    {
        ActionRegister::register(SearchLogAction::class);
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
