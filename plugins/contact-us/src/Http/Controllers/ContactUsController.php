<?php

namespace Progmix\ContactUs\Http\Controllers;

use Juzaweb\CMS\Http\Controllers\BackendController;
use Juzaweb\CMS\Traits\ResourceController;
use Progmix\ContactUs\Http\Datatables\ContactUsDatatable;
use Progmix\ContactUs\Models\Contact;
use Illuminate\Support\Facades\Validator;

class ContactUsController extends BackendController
{
    use ResourceController {
        getDataForForm as DataForForm;
        afterSave as tAfterSave;
    }

    protected $viewPrefix = 'contact::backend';

    protected function getDataTable(...$params)
    {
        return new ContactUsDatatable();
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
            'subject' => 'required|string|max:191',
            'message' => 'required|string|max:191',
        ]);

        return $validator;
    }

    protected function getModel(...$params)
    {
        return Contact::class;
    }

    protected function getTitle(...$params)
    {
        return trans('contact::content.title');
    }

    protected function getDataForForm($model, ...$params): array
    {
        $data = $this->DataForForm($model);
        return $data;
    }
}

