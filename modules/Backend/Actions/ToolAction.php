<?php

namespace Juzaweb\Backend\Actions;

use Juzaweb\CMS\Abstracts\Action;
use Juzaweb\CMS\Facades\HookAction;

class ToolAction extends Action
{
    public function handle()
    {
        $this->addAction(Action::BACKEND_INIT, [$this, 'addAdminMenu']);
    }

    public function addAdminMenu()
    {
        HookAction::addAdminMenu(
            trans_cms('cms::app.tools'),
            'tools',
            [
                'icon' => 'fa fa-cogs',
                'position' => 99,
            ]
        );



        if (!config('network.enable')) {
            HookAction::addAdminMenu(
                'Log Viewer',
                'log-viewer',
                [
                    'icon' => 'fa fa-history',
                    'position' => 20,
                    'turbolinks' => false,
                    'parent' => 'tools',
                ]
            );
        }
    }
}
