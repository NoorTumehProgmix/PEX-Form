<?php

namespace Progmix\SearchLog\Actions;

use Juzaweb\Backend\Models\Menu;
use Juzaweb\CMS\Abstracts\Action;
use Juzaweb\CMS\Facades\HookAction;

class SearchLogAction extends Action
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
            trans('SearchLog::content.name'),
            'search',
            [
                'icon'        => 'fa fa-search',
                'position'    => 50,
                'permissions' => [
                    'search',
                ],

            ]
        );
        HookAction::addAdminMenu(
            trans('SearchLog::content.search_log'),
            'search-log',
            [
                'icon'     => 'fa fa-search',
                'position' => 50,
                'parent'   => 'search',

                'permissions' => [
                    'search.index',
                ],
            ]
        );

        $quickMenu = Menu::where('type', 'quick-links')->where('lang', app()->getLocale())->first();
        if ($quickMenu) {
            HookAction::addAdminMenu(
                trans('SearchLog::content.quick_links'),
                "menus/" . $quickMenu->id,
                [
                    'icon'     => 'fa fa-search',
                    'position' => 50,
                    'parent'   => 'search',

                    'permissions' => [
                        'search.index',
                    ],

                ]
            );
        }
    }

    public function addAdminMenu()
    {
        $this->hookAction->registerPermissionGroup(
            'search',
            [
                'name'        => "search",
                'description' => "search",
                'key'         => "search",
            ]
        );
        $this->hookAction->registerPermission(
            "search_index",
            [
                'name'        => "search.index",
                'group'       => "search",
                'description' => "View Search Logs",
                'key'         => "search",

            ]
        );

        $this->hookAction->registerPermission(
            "search_index",
            [
                'name'        => "search.edit",
                'group'       => "search",
                'description' => "View Search Log",
                'key'         => "search",

            ]
        );

        $this->hookAction->registerPermission(
            "quick_index",
            [
                'name'        => "search.quick.index",
                'group'       => "search",
                'description' => "View Quick Links",
                'key'         => "search",

            ]
        );

        $this->hookAction->registerPermission(
            "quick_index",
            [
                'name'        => "search.quick.edit",
                'group'       => "search",
                'description' => "Edit Quick Links",
                'key'         => "search",

            ]
        );
    }
}
