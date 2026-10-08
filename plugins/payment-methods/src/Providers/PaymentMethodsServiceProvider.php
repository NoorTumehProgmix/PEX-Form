<?php

namespace Progmix\PaymentMethods\Providers;

use Juzaweb\CMS\Facades\ActionRegister;
use Juzaweb\CMS\Support\ServiceProvider;
use Progmix\PaymentMethods\Actions\PaymentMethodsAction;

class PaymentMethodsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        ActionRegister::register([PaymentMethodsAction::class]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/payment_methods.php',
            'payment_methods'
        );
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
