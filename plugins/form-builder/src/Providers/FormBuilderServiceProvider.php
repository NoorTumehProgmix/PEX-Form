<?php

namespace Progmix\FormBuilder\Providers;

use Juzaweb\CMS\Support\ServiceProvider;
use Juzaweb\CMS\Facades\ActionRegister;
use Illuminate\Support\Facades\Validator;

use Progmix\FormBuilder\Actions\FormBuilderAction as ActionsFormBuilderAction;
use Progmix\FormBuilder\Rules\maxWordsValidator;
use Progmix\FormBuilder\Rules\minWordsValidator;
use Progmix\FormBuilder\Rules\onlyAvailableItemsValidator;
use Progmix\FormBuilder\Rules\uniqueJsonValidator;

class FormBuilderServiceProvider extends ServiceProvider
{
    public function boot()
    {
        ActionRegister::register(ActionsFormBuilderAction::class);
        Validator::extend('maxWords', function ($attribute, $value, $parameters, $validator) {
            return (new maxWordsValidator($parameters[0]))->passes($attribute, $value);
        });
        Validator::extend('minWords', function ($attribute, $value, $parameters, $validator) {
            return (new minWordsValidator($parameters[0]))->passes($attribute, $value);
        });
        Validator::extend('uniqueJson', function ($attribute, $value, $parameters, $validator) {
            return (new uniqueJsonValidator($parameters[0]))->passes($attribute, $value);
        });
        Validator::extend('onlyAvailableItems', function ($attribute, $value, $parameters, $validator) {
            return (new onlyAvailableItemsValidator($parameters[0]))->passes($attribute, $value);
        });
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
