<?php

namespace Progmix\Api\Actions;

use Juzaweb\CMS\Abstracts\Action;
use Juzaweb\CMS\Facades\HookAction;

class ApiAction extends Action
{

    public function handle()
    {
        $this->addAction(Action::INIT_ACTION, [$this, 'registerResource']);
        $this->addAction(Action::BACKEND_INIT, [$this, 'addAdminMenu']);

    }

    public function registerResource()
    {
        HookAction::addAdminMenu(
            'API',
            'api',
            [
                'icon'        => 'fa fa-cloud',
                'position'    => 30,
                'permissions' => [
                    'api.index',
                ],

            ]
        );

        HookAction::addAdminMenu(
            'API Log',
            'log-api',
            [
                'icon'        => 'fa fa-arrow-right',
                'position'    => 2,
                'parent'      => 'api',
                'permissions' => [
                    'api.index',
                ],

            ]
        );

        HookAction::addAdminMenu(
            'All API\'s',
            'api',
            [
                'icon'        => 'fa fa-arrow-right',
                'position'    => 1,
                'parent'      => 'api',
                'permissions' => [
                    'api.index',
                ],
            ]
        );
    }
    
    public function addAdminMenu()
    {
        $this->hookAction->registerPermissionGroup(
            'api',
            [
                'name' => "api",
                'description' => "API",
                'key' => "api",
            ]
        );
        $this->hookAction->registerPermission(
            "api",
            [
                'name' => "api.index",
                'group' => "api",
                'description' => "View List API",
                'key' => "api",

            ]
        );
        $this->hookAction->registerPermission(
            "api_edit",
            [
                'name' => "api.edit",
                'group' => "api",
                'description' => "Edit List API",
                'key' => "api",

            ]
        );
        $this->hookAction->registerPermission(
            "api_create",
            [
                'name' => "api.create",
                'group' => "api",
                'description' => "Create List API",
                'key' => "api",

            ]
        );
        $this->hookAction->registerPermission(
            "api_delete",
            [
                'name' => "api.delete",
                'group' => "api",
                'description' => "Delete List API",
                'key' => "api",

            ]
        );
    }

}
