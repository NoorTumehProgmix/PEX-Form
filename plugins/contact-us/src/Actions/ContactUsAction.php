<?php

namespace Progmix\ContactUs\Actions;

use Juzaweb\CMS\Abstracts\Action;
use Juzaweb\CMS\Facades\HookAction;

class ContactUsAction extends Action
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
            trans_cms('contact::content.title'),
            'contact-us',
            [
                'icon'        => 'fa fa-address-book',
                'position'    => 30,
                'permissions' => [
                    'contact.index',
                ],
            ]
        );

        $this->hookAction->registerPermissionGroup(
            'contact',
            [
                'name'        => trans('contact::content.title'),
                'description' => trans('contact::content.title'),
                'key'         => "contact",
            ]
        );

        $this->hookAction->registerPermission(
            "contact-index",
            [
                'name'        => "contact.index",
                'group'       => "contact",
                'description' => "View List Contact Us",
                'key'         => "contact",

            ]
        );
        $this->hookAction->registerPermission(
            "contact-edit",
            [
                'name'        => "contact.edit",
                'group'       => "contact",
                'description' => "View Contact",
                'key'         => "contact",

            ]
        );
    }
}
