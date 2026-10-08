<?php

namespace Juzaweb\Subscriptions\Actions;

use Juzaweb\CMS\Abstracts\Action;
use Juzaweb\CMS\Facades\HookAction;

class SubscriptionsAction extends Action
{
    /**
     * Execute the actions.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->addAction(Action::INIT_ACTION, [$this, 'registerResource']);
        $this->addAction(Action::BACKEND_INIT, [$this, 'addAdminMenu']);
    }
    public function registerResource()
    {
        HookAction::addAdminMenu(
            trans('subscriptions::content.subscriptions'),
            'subscriptions',
            [
                'icon'        => 'fa fa-envelope',
                'position'    => 50,
                'permissions' => [
                    'subscriptions',
                ],

            ]
        );
    }
    public function addAdminMenu()
    {
        $this->hookAction->registerPermissionGroup(
            'subscriptions',
            [
                'name' => "subscriptions",
                'description' => "subscriptions",
                'key' => "subscriptions",
            ]
        );
        $this->hookAction->registerPermission(
            "subscriptions_index",
            [
                'name' => "subscriptions.index",
                'group' => "subscriptions",
                'description' => "View List Subscriptions",
                'key' => "subscriptions",

            ]
        );
        $this->hookAction->registerPermission(
            "subscriptions_delete",
            [
                'name' => "subscriptions.delete",
                'group' => "subscriptions",
                'description' => "Delete Subscriptions",
                'key' => "subscriptions",

            ]
        );
    }
}
