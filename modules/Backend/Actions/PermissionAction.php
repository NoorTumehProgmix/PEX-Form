<?php



namespace Juzaweb\Backend\Actions;

use Illuminate\Support\Arr;
use Juzaweb\CMS\Abstracts\Action;
use Juzaweb\CMS\Facades\HookAction;
use Juzaweb\CMS\Models\User;

class PermissionAction extends Action
{
    public function handle()
    {
        $this->addAction(
            Action::BACKEND_INIT,
            [$this, 'addAdminMenu']
        );

        $this->addAction(
            Action::PERMISSION_INIT,
            [$this, 'addPermissions']
        );

        $this->addAction(
            Action::BACKEND_USER_FORM_RIGHT,
            [$this, 'addRoleUserForm']
        );

        $this->addAction(
            Action::BACKEND_USER_AFTER_SAVE,
            [$this, 'saveRoleUser'],
            20,
            2
        );
    }

    public function saveRoleUser($data, User $model)
    {
        $roles = Arr::get($data, 'roles', []);

        $model->syncRoles($roles);
    }

    public function addRoleUserForm(User $model)
    {
        echo e(
            view(
                'cms::backend.role.components.role_users',
                compact('model')
            )
        );
    }

    public function addAdminMenu()
    {
        HookAction::addAdminMenu(
            trans_cms('cms::app.roles'),
            'roles',
            [
                'icon' => 'fa fa-users',
                'position' => 45,
                'parent' => 'managements',
                'permissions' => [
                    'roles.index',
                ],
            ]
        );
    }

    public function addPermissions(): void
    {
        /*HookAction::registerResourcePermissions(
            'media',
            trans_cms('cms::app.media')
        );
        */

        HookAction::registerResourcePermissions(
            'plugins',
            trans_cms('cms::app.plugin')
        );

        HookAction::registerResourcePermissions(
            'users',
            trans_cms('cms::app.user')
        );

        HookAction::registerResourcePermissions(
            'email_templates',
            trans_cms('cms::app.email_template')
        );

        HookAction::registerResourcePermissions(
            'media',
            trans_cms('cms::app.media')
        );

        HookAction::registerResourcePermissions(
            'menus',
            trans_cms('cms::app.menu')
        );
        HookAction::registerResourcePermissions(
            'theme_setting',
            trans_cms('cms::app.setting')
        );

        HookAction::registerResourcePermissions(
            'roles',
            trans_cms('cms::app.role')
        );
        HookAction::registerResourcePermissions(
            'translations',
            trans_cms('cms::app.translations')
        );
        // HookAction::registerResourcePermissions(
        //     'log_viewer',
        //     trans_cms('cms::app.error_logs')
        // );

        $this->hookAction->registerPermissionGroup(
            'actions_logs',
            [
                'name' => "actions_logs",
                'description' => "Activity Logs",
            ]
        );
        $this->hookAction->registerPermission(
            "actions_logs.index",
            [
                'name' => "actions_logs.index",
                'group' => "actions_logs",
                'description' => "Activity Logs",
            ]
        );

        // Email Logs
        $this->hookAction->registerPermissionGroup(
            'email_logs',
            [
                'name' => "email_logs",
                'description' => "Email Logs",
            ]
        );
        $this->hookAction->registerPermission(
            "email_logs.index",
            [
                'name' => "email_logs.index",
                'group' => "email_logs",
                'description' => "List Email Logs",
            ]
        );
        $this->hookAction->registerPermission(
            "email_logs.delete",
            [
                'name' => "email_logs.delete",
                'group' => "email_logs",
                'description' => "Delete Email",
            ]
        );

        $this->hookAction->registerPermission(
            "email_logs.resend",
            [
                'name' => "email_logs.resend",
                'group' => "email_logs",
                'description' => "Resend Email",
            ]
        );
        $this->hookAction->registerPermission(
            "email_logs.cancel",
            [
                'name' => "email_logs.cancel",
                'group' => "email_logs",
                'description' => "Cancel Email",
            ]
        );




        // Settings Permissions
        $this->hookAction->registerPermissionGroup(
            'settings',
            [
                'name' => "settings",
                'description' => "Settings",
            ]
        );
        $this->hookAction->registerPermission(
            "general",
            [
                'name' => "settings.general",
                'group' => "settings",
                'description' => "General Settings",
            ]
        );
        $this->hookAction->registerPermission(
            "prefix",
            [
                'name' => "settings.prefix",
                'group' => "settings",
                'description' => "CMS Generate Prefix",
            ]
        );
        $this->hookAction->registerPermission(
            "hosts",
            [
                'name' => "settings.hosts",
                'group' => "settings",
                'description' => "Hosts",
            ]
        );
        $this->hookAction->registerPermission(
            "templates",
            [
                'name' => "settings.templates",
                'group' => "settings",
                'description' => "Templates Management",
            ]
        );
        $this->hookAction->registerPermission(
            "seo",
            [
                'name' => "settings.seo",
                'group' => "settings",
                'description' => "SEO Settings",
            ]
        );
        $this->hookAction->registerPermission(
            "social-links",
            [
                'name' => "settings.social-links",
                'group' => "settings",
                'description' => "Social Links",
            ]
        );

        $this->hookAction->registerPermission(
            "email_settings",
            [
                'name' => "settings.email",
                'group' => "settings",
                'description' => "Email Settings",
            ]
        );
        $this->hookAction->registerPermission(
            "backup",
            [
                'name' => "settings.backup",
                'group' => "settings",
                'description' => "Backup Settings",
            ]
        );

        $this->hookAction->registerPermission(
            "permalinks",
            [
                'name' => "settings.permalinks",
                'group' => "settings",
                'description' => "Permalinks Settings",
            ]
        );
        $this->hookAction->registerPermission(
            "options-media",
            [
                'name' => "settings.options-media",
                'group' => "settings",
                'description' => "Media Settings",
            ]
        );

        HookAction::registerResourcePermissions(
            'branches_and_working_hours',
            trans_cms('cms::app.branches_and_working_hours')
        );

        HookAction::registerResourcePermissions(
            'branches',
            trans_cms('cms::app.branches')
        );

        HookAction::registerResourcePermissions(
            'tags',
            trans_cms('cms::app.tags')
        );

        HookAction::registerResourcePermissions(
            'types',
            trans_cms('cms::app.types')
        );

        HookAction::registerResourcePermissions(
            'regions',
            trans_cms('cms::app.regions')
        );

        HookAction::registerResourcePermissions(
            'cities',
            trans_cms('cms::app.cities')
        );

        HookAction::registerResourcePermissions(
            'status_group',
            trans_cms('cms::app.status_group')
        );

        HookAction::registerResourcePermissions(
            'messages',
            trans_cms('cms::app.messages')
        );

        // API Permissions
        // $this->hookAction->registerPermissionGroup(
        //     'api',
        //     [
        //         'name' => "api",
        //         'description' => "API's",
        //     ]
        // );
        // $this->hookAction->registerPermission(
        //     "front_api",
        //     [
        //         'name' => "api.frontend",
        //         'group' => "api",
        //         'description' => "Frontend",
        //     ]
        // );
        // $this->hookAction->registerPermission(
        //     "admin_api",
        //     [
        //         'name' => "api.admin",
        //         'group' => "api",
        //         'description' => "Admin",
        //     ]
        // );
    }
}
