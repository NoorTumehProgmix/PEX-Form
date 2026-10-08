<?php

namespace Progmix\Redirections\Actions;

use Juzaweb\CMS\Abstracts\Action;
use Juzaweb\CMS\Facades\HookAction;

class RedirectionsAction extends Action
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
            trans('redirections::content.name'),
            'redirections',
            [
                'icon'        => 'fa fa-refresh',
                'position'    => 50,
                'permissions' => [
                    'redirections.index',
                ],

            ]
        );


    }
    public function addAdminMenu()
    {
        HookAction::registerResourcePermissions(
            'redirections',
            trans_cms('redirections::content.name')
        );
    }
}
