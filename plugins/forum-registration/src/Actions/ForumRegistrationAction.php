<?php

namespace Progmix\ForumRegistration\Actions;

use Juzaweb\CMS\Abstracts\Action;
use Juzaweb\CMS\Facades\HookAction;

class ForumRegistrationAction extends Action
{
    public function handle(): void
    {
        $this->addAction(Action::BACKEND_INIT, [$this, 'addAdminMenus']);
    }

    public function addAdminMenus(): void
    {
        HookAction::addAdminMenu(
            trans_cms('forum-registration::content.title'),
            'forum-registrations',
            [
                'icon' => 'fa fa-id-card',
                'position' => 31,
                'permissions' => [
                    'forum_registration.index',
                ],
            ]
        );

        $this->hookAction->registerPermissionGroup(
            'forum_registration',
            [
                'name' => trans('forum-registration::content.title'),
                'description' => trans('forum-registration::content.title'),
                'key' => 'forum_registration',
            ]
        );

        $this->hookAction->registerPermission(
            'forum-registration-index',
            [
                'name' => 'forum_registration.index',
                'group' => 'forum_registration',
                'description' => 'View forum registration list',
                'key' => 'forum_registration',
            ]
        );

        $this->hookAction->registerPermission(
            'forum-registration-edit',
            [
                'name' => 'forum_registration.edit',
                'group' => 'forum_registration',
                'description' => 'View forum registration',
                'key' => 'forum_registration',
            ]
        );
    }
}
