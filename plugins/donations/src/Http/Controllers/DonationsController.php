<?php

namespace Progmix\Donations\Http\Controllers;

use Juzaweb\CMS\Http\Controllers\BackendController;
use Juzaweb\CMS\Traits\ResourceController;
use Progmix\Donations\Http\Datatables\DonationsDatatable;
use Progmix\Donations\Models\Donation;
use Illuminate\Support\Facades\Validator;

class DonationsController extends BackendController
{
    use ResourceController {
        getDataForForm as DataForForm;
        afterSave as tAfterSave;
    }

    protected $viewPrefix = 'donations::backend';

    protected function getDataTable(...$params)
    {
        return new DonationsDatatable();
    }

    protected function validator(array $attributes, ...$params)
    {
        $validator = Validator::make($attributes, [
            'name'    => 'required|string|max:191',
            'email'   => 'required|email|max:191',
            'phone'   => [
                'required',
                'regex:/^(?:(?:(\+?972|\(\+?972\)|\+?\(972\)|\+?970|\(\+?970\)|\+?\(970\))(?:\s|\.|-)?([1-9]\d?))|(0[23489]{1})|(0[57]{1}[0-9]))(?:\s|\.|-)?([^0\D]{1}\d{2}(?:\s|\.|-)?\d{4})$/',
                'min:8',
                'max:20',
            ],
            'payment_method' => 'required|string|max:191',
            'payment_status' => 'required|string|max:191',
            'payment_id' => 'required|string|max:191',
            'payment_amount' => 'required|string|max:191',
            'payment_currency' => 'required|string|max:191',
            'recurring' => 'required|string|max:191',
            'recurring_amount' => 'required|string|max:191',
            'recurring_interval' => 'required|string|max:191',
            'note' => 'required|string|max:191',
        ]);

        return $validator;
    }

    protected function getModel(...$params)
    {
        return Donation::class;
    }

    protected function getTitle(...$params)
    {
        return trans('donations::content.title');
    }

    protected function getDataForForm($model, ...$params): array
    {
        $data = $this->DataForForm($model);
        return $data;
    }
}
