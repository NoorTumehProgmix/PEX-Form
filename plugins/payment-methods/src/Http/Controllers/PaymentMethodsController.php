<?php

namespace Progmix\PaymentMethods\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Juzaweb\CMS\Http\Controllers\BackendController;
use Juzaweb\CMS\Traits\ResourceController;
use Progmix\PaymentMethods\Http\Datatables\PaymentMethodsDatatable;
use Progmix\PaymentMethods\Models\PaymentMethod;

class PaymentMethodsController extends BackendController
{
    use ResourceController {
        getDataForForm as DataForForm;
    }

    protected string $viewPrefix = 'paymentMethods::backend';

    protected function getDataTable(...$params): PaymentMethodsDatatable
    {
        return new PaymentMethodsDatatable();
    }

    protected function validator(array $attributes, ...$params): \Illuminate\Validation\Validator
    {
        $types = config('payment_methods.methods');
        $types = array_keys($types);

        return Validator::make(
            $attributes,
            [
                'type' => [
                    'required_if:id,',
                    Rule::in($types),
                ],
                'name' => [
                    'required',
                ],
            ]
        );
    }

    protected function getModel(...$params): string
    {
        return PaymentMethod::class;
    }

    protected function getTitle(...$params): string
    {
        return trans('paymentMethods::content.title');
    }

    protected function getDataForForm($model, ...$params): array
    {
        $data            = $this->DataForForm($model);
        $data['methods'] = PaymentMethod::getPaymentMethods();
        return $data;
    }

    protected function parseDataForSave(array $attributes, ...$params): array
    {
        $attributes['active'] = $attributes['active'] ?? 0;

        return $attributes;
    }
}
