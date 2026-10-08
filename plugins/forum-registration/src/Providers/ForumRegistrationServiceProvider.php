<?php

namespace Progmix\ForumRegistration\Providers;

use Juzaweb\CMS\Facades\ActionRegister;
use Juzaweb\CMS\Support\ServiceProvider;
use Progmix\ForumRegistration\Actions\ForumRegistrationAction;

class ForumRegistrationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        ActionRegister::register([ForumRegistrationAction::class]);
    }

    public function register(): void
    {
        //
    }

    public function provides(): array
    {
        return [];
    }
}
