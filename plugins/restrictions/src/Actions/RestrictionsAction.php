<?php

namespace Progmix\Restrictions\Actions;

use Juzaweb\CMS\Abstracts\Action;
use Juzaweb\CMS\Facades\HookAction;

class RestrictionsAction extends Action
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
            trans('restrictions::content.name'),
            'restrictions',
            [
                'icon'        => 'fa fa-ban',
                'position'    => 50,
                'permissions' => [
                    'restrictions.index',
                ],
            ]
        );

        $this->hookAction->registerPermissionGroup(
            'restrictions',
            [
                'name'        => trans('restrictions::content.name'),
                'description' => trans('restrictions::content.name'),
                'key'         => "contact",
            ]
        );

        $this->hookAction->registerPermission(
            "restrictions-index",
            [
                'name'        => "restrictions.index",
                'group'       => "restrictions",
                'description' => "View List Restrictions",
                'key'         => "restrictions",

            ]
        );
        $this->hookAction->registerPermission(
            "restrictions-create",
            [
                'name'        => "restrictions.create",
                'group'       => "restrictions",
                'description' => "Create restriction",
                'key'         => "restrictions",

            ]
        );
        $this->hookAction->registerPermission(
            "restrictions-delete",
            [
                'name'        => "restrictions.delete",
                'group'       => "restrictions",
                'description' => "Delete restriction",
                'key'         => "restrictions",

            ]
        );
        $this->hookAction->registerPermission(
            "restrictions-edit",
            [
                'name'        => "restrictions.edit",
                'group'       => "restrictions",
                'description' => "Edit restriction",
                'key'         => "restrictions",

            ]
        );
    }

}
