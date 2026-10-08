<?php

namespace Progmix\Donations\Actions;

use Juzaweb\CMS\Abstracts\Action;
use Juzaweb\CMS\Facades\HookAction;

class DonationsAction extends Action
{
    /**
     * Execute the actions.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->addAction(Action::BACKEND_INIT, [$this, 'addAdminMenus']);
    }

    public function addAdminMenus()
    {
        HookAction::addAdminMenu(
            trans_cms('donations::content.title'),
            'donations',
            [
                'icon'        => 'fa fa-address-book',
                'position'    => 30,
                'group'       => 'donation',
                'permissions' => [
                    'donations.index',
                ],
            ]
        );

        $this->hookAction->registerPermissionGroup(
            'donations',
            [
                'name'        => trans('donations::content.title'),
                'description' => trans('donations::content.title'),
                'key'         => "donations",
            ]
        );

        $this->hookAction->registerPermission(
            "donations-index",
            [
                'name'        => "donations.index",
                'group'       => "donations",
                'description' => "View List Donations",
                'key'         => "donations",

            ]
        );
        $this->hookAction->registerPermission(
            "donations-edit",
            [
                'name'        => "donations.edit",
                'group'       => "donations",
                'description' => "View Donations",
                'key'         => "donations",

            ]
        );
    }
}
