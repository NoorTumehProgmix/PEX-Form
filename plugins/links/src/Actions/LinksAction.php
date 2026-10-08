<?php

namespace Progmix\Links\Actions;

use Juzaweb\CMS\Abstracts\Action;
use Juzaweb\CMS\Facades\HookAction;

class LinksAction extends Action
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
            trans('links::content.name'),
            'links',
            [
                'icon'        => 'fa fa-link',
                'position'    => 50,
                'permissions' => [
                    'links',
                ],

            ]
        );


    }
    public function addAdminMenu()
    {
        $this->hookAction->registerPermissionGroup(
            'links',
            [
                'name' => "links",
                'description' => "links",
                'key' => "links",
            ]
        );
        $this->hookAction->registerPermission(
            "links_index",
            [
                'name' => "links.index",
                'group' => "links",
                'description' => "View General links List",
                'key' => "links",

            ]
        );
        $this->hookAction->registerPermission(
            "links_edit",
            [
                'name' => "links.edit",
                'group' => "links",
                'description' => "Edit General Links",
                'key' => "links",

            ]
        );
        $this->hookAction->registerPermission(
            "links_create",
            [
                'name' => "links.create",
                'group' => "links",
                'description' => "Create General Links",
                'key' => "links",

            ]
        );
        $this->hookAction->registerPermission(
            "links_delete",
            [
                'name' => "links.delete",
                'group' => "links",
                'description' => "Delete General Links",
                'key' => "links",

            ]
        );
    }
}
