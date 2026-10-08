<?php

namespace Progmix\FormBuilder\Actions;


use Juzaweb\CMS\Abstracts\Action;
use Juzaweb\CMS\Facades\HookAction;

class FormBuilderAction extends Action
{
    /**
     * Execute the actions.
     *
     * @return void
     */
    public function handle()
    {
        $this->addAction(Action::PERMISSION_INIT, [$this, 'addPermissions']);
        $this->addAction(Action::BACKEND_INIT, [$this, 'addAdminMenu']);
    }

    public function addAdminMenu()
    {
        HookAction::addAdminMenu(
            trans('cms::app.form_builder'),
            'form-builder',
            [
                'icon'        => 'fa fa-columns',
                'position'    => 30,
                'permissions' => [
                    'formBuilder.index',
                ],

            ]
        );

        HookAction::addAdminMenu(
            trans('cms::app.translations_form'),
            'translations/progmix_form_builder',
            [
                'icon'        => 'fa fa-arrow-right',
                'position'    => 3,
                'parent'      => 'form-builder',
                'permissions' => [
                    'formBuilder.index',
                ],

            ]
        );

        HookAction::addAdminMenu(
            trans('cms::app.forms_builder'),
            'form-builder',
            [
                'icon'        => 'fa fa-arrow-right',
                'position'    => 1,
                'parent'      => 'form-builder',
                'permissions' => [
                    'formBuilder.index',
                ],
            ]
        );

        HookAction::addAdminMenu(
            trans('cms::app.forms_Submissions'),
            'form-submissions',
            [
                'icon'        => 'fa fa-arrow-right',
                'position'    => 2,
                'parent'      => 'form-builder',
                'permissions' => [
                    'formBuilder.index',
                ],
            ]
        );
    }


    public function addPermissions()
    {
        $this->hookAction->registerPermissionGroup(
            'formBuilder',
            [
                'name' => "formBuilder",
                'description' => "Form Builder",
                'key' => "formBuilder",
            ]
        );

        $this->hookAction->registerPermission(
            "formBuilder_index",
            [
                'name' => "formBuilder.index",
                'group' => "formBuilder",
                'description' => "View List Form Builder",
                'key' => "formBuilder",
            ]
        );

        $this->hookAction->registerPermission(
            "formBuilder_edit",
            [
                'name' => "formBuilder.edit",
                'group' => "formBuilder",
                'description' => "Edit List Form Builder",
                'key' => "formBuilder",
            ]
        );
        $this->hookAction->registerPermission(
            "formBuilder_create",
            [
                'name' => "formBuilder.create",
                'group' => "formBuilder",
                'description' => "Create List Form Builder",
                'key' => "formBuilder",
            ]
        );

        $this->hookAction->registerPermission(
            "formBuilder_delete",
            [
                'name' => "formBuilder.delete",
                'group' => "formBuilder",
                'description' => "Delete List Form Builder",
                'key' => "formBuilder",

            ]
        );
    }
}
