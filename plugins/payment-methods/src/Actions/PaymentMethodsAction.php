<?php

namespace Progmix\PaymentMethods\Actions;

use Juzaweb\CMS\Abstracts\Action;
use Juzaweb\CMS\Facades\HookAction;

class PaymentMethodsAction extends Action
{
    /**
     * Execute the actions.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->addAction(Action::PERMISSION_INIT, [$this, 'addPermissions']);
        $this->addAction(Action::BACKEND_INIT, [$this, 'addAdminMenus']);
    }

    public function addAdminMenus()
    {
        HookAction::registerAdminPage(
            'payment-methods',
            [
                'title' => trans('paymentMethods::content.title'),
                'menu'  => [
                    'icon'        => 'fa fa-credit-card',
                    'position'    => 25,
                    'group'    => 'donation',
                    'permissions' => [
                        'paymentMethods.index',
                    ]
                ],
            ]
        );
    }

    public function addPermissions()
    {
        $this->hookAction->registerPermissionGroup(
            'paymentMethods',
            [
                'name'        => trans('paymentMethods::content.title'),
                'description' => trans('paymentMethods::content.title'),
                'key'         => "paymentMethods",
            ]
        );

        $this->hookAction->registerPermission(
            "paymentMethods-index",
            [
                'name'        => "paymentMethods.index",
                'group'       => "paymentMethods",
                'description' => "View payment methods List",
                'key'         => "paymentMethods",

            ]
        );

        $this->hookAction->registerPermission(
            "paymentMethods-edit",
            [
                'name'        => "paymentMethods.edit",
                'group'       => "paymentMethods",
                'description' => "Edit payment methods",
                'key'         => "paymentMethods",

            ]
        );

        $this->hookAction->registerPermission(
            "paymentMethods_create",
            [
                'name'        => "paymentMethods.create",
                'group'       => "paymentMethods",
                'description' => "Create payment method",
                'key'         => "paymentMethods",
            ]
        );

        $this->hookAction->registerPermission(
            "paymentMethods_delete",
            [
                'name'        => "paymentMethods.delete",
                'group'       => "paymentMethods",
                'description' => "Delete payment method",
                'key'         => "paymentMethods",

            ]
        );
    }
}
