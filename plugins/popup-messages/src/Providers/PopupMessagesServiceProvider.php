<?php

namespace Progmix\PopupMessages\Providers;

use Juzaweb\CMS\Facades\ActionRegister;
use Juzaweb\CMS\Support\ServiceProvider;
use Progmix\PopupMessages\PopupMessagesSliderAction;

class PopupMessagesServiceProvider extends ServiceProvider
{
    public function boot()
    {
        ActionRegister::register(PopupMessagesSliderAction::class);
    }
}
