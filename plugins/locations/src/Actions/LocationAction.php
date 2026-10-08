<?php

namespace Progmix\Locations\Actions;

use Juzaweb\CMS\Abstracts\Action;
use Juzaweb\CMS\Facades\HookAction;

class LocationAction extends Action
{
    /**
     * Execute the actions.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->addAction(Action::BACKEND_INIT, [$this, 'addAdminMenu']);
    }


    public function addAdminMenu()
    {
        //Locations
        HookAction::registerAdminPage(
            'locations',
            [
                'title' => trans('loc::content.locations'),
                'menu' => [
                    'icon' => 'fa fa-map-marker',
                    'group'    => 'donation',
                    'position' => 50,
                ]
            ]
        );
        HookAction::addAdminMenu(
            trans('loc::content.countries'),
            'locations.countries',
            [
                'position' => 1,
                'parent' => 'locations',
                'permissions' => [
                    'locations.index',
                ],

            ]
        );
        HookAction::addAdminMenu(
            trans('loc::content.states'),
            'locations.states',
            [
                'position' => 1,
                'parent' => 'locations',
                'permissions' => [
                    'locations.index',
                ],

            ]
        );
        HookAction::addAdminMenu(
            trans('loc::content.cities'),
            'locations.cities',
            [
                'position' => 1,
                'parent' => 'locations',
                'permissions' => [
                    'locations.index',
                ],

            ]
        );


        HookAction::registerResourcePermissions(
            'locations',
            trans_cms('cms::app.locations')
        );
    }
}
