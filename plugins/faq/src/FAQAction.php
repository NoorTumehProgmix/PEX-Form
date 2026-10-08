<?php

namespace Juzaweb\FAQ;

use Juzaweb\Backend\Models\Post;
use Juzaweb\CMS\Abstracts\Action;
use Juzaweb\CMS\Facades\HookAction;

class FAQAction extends Action
{
    public function handle()
    {
        $this->addAction(Action::INIT_ACTION, [$this, 'registerPost']);
        $this->addAction(Action::PERMISSION_INIT, [$this, 'addPermissions']);
    }

    public function registerPost()
    {
        HookAction::registerPostType(
            'faqs',
            [
                'label'         => trans_cms('faq::content.faqs'),
                'model'         => Post::class,
                'menu_icon'     => 'fa fa-edit',
                'callback'      => PostController::class,
                'menu_position' => 18,
                'metas'         => [],
            ]
        );

        HookAction::registerTaxonomy(
            'categories',
            'faqs',
            [
                'label' => trans_cms('faq::content.categories'),
                'priority' => 5,
                'menu_position' => 5,
                'supports' => ['display_order' => 'display_order'],
            ]
        );
    }

    public function addPermissions()
    {
        $this->hookAction->registerPermissionGroup(
            'faq',
            [
                'name' => "faq",
                'description' => "faq",
                'key' => "faq",
            ]
        );

        $this->hookAction->registerPermission(
            "faq_index",
            [
                'name' => "faq.index",
                'group' => "faq",
                'description' => "View List Form Builder",
                'key' => "faq",
            ]
        );

        $this->hookAction->registerPermission(
            "faq_edit",
            [
                'name' => "faq.edit",
                'group' => "faq",
                'description' => "Edit List Form Builder",
                'key' => "faq",
            ]
        );
        $this->hookAction->registerPermission(
            "faq_create",
            [
                'name' => "faq.create",
                'group' => "faq",
                'description' => "Create List Form Builder",
                'key' => "faq",
            ]
        );

        $this->hookAction->registerPermission(
            "faq_delete",
            [
                'name' => "faq.delete",
                'group' => "faq",
                'description' => "Delete List Form Builder",
                'key' => "faq",

            ]
        );
    }
}
