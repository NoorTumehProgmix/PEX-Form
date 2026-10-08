<?php

namespace Juzaweb\FAQ\Providers;

use Juzaweb\CMS\Facades\ActionRegister;
use Juzaweb\CMS\Support\ServiceProvider;
use Juzaweb\FAQ\FAQAction;

class FAQServiceProvider extends ServiceProvider
{
    public function boot()
    {
        ActionRegister::register(FAQAction::class);
    }
}
