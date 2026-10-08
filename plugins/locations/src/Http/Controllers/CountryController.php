<?php

namespace Progmix\Locations\Http\Controllers;

use Juzaweb\CMS\Traits\ResourceController;
use Progmix\Locations\Http\Datatables\CountryDatatable;
use Juzaweb\CMS\Http\Controllers\BackendController;
use Progmix\Locations\Models\Country;
use Illuminate\Support\Facades\Lang;


class CountryController extends BackendController
{
    use ResourceController {
        getDataForForm as DataForForm;
    }
    protected $viewPrefix = 'loc::backend..country';

    protected function getDataTable(...$params)
    {
        return new CountryDatatable();
    }
    protected function validator(array $attributes, ...$params)
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'code' => ['required', 'string', 'max:2'],
        ];
    }

    protected function getModel(...$params)
    {
        return Country::class;
    }
    protected function getTitle(...$params)
    {
        return trans('loc::content.countries');
    }

    protected function getDataForForm($model, ...$params): array
    {
        $data = $this->DataForForm($model);
        return $data;
    }

}
