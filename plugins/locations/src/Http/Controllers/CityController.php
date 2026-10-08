<?php

namespace Progmix\Locations\Http\Controllers;

use Juzaweb\CMS\Traits\ResourceController;
use Progmix\Locations\Http\Datatables\CityDatatable;
use Juzaweb\CMS\Http\Controllers\BackendController;
use Progmix\Locations\Models\City;
use Progmix\Locations\Models\State;
use Illuminate\Support\Facades\Lang;

class CityController extends BackendController
{
    use ResourceController {
        getDataForForm as DataForForm;
    }
    protected $viewPrefix = 'loc::backend..city';

    protected function getDataTable(...$params)
    {
        return new CityDatatable();
    }
    protected function validator(array $attributes, ...$params)
    {
        return [
            'name' => ['required', 'string', 'max:100'],

        ];
    }

    protected function getModel(...$params)
    {
        return City::class;
    }
    protected function getTitle(...$params)
    {
        return trans('loc::content.cities');
    }

    protected function getDataForForm($model, ...$params): array
    {

        $data = $this->DataForForm($model);
        $query = State::query();
        $data['states'] = $query->get();

        return $data;
    }

    protected function BeforeSave($data, $model, ...$params): void
    {
        // dd($data, $model);
        // $locales = config('app.locales');
        // $names = [];
        // if (isset($model->getAttributes()['name'])) {
        //     $names = json_decode($model->getAttributes()['name'], true) ?? [];
        // }
        // $names[Lang::locale()] = $data['name'];
        // foreach ($locales as $key => $locale) {
        //     if (!isset($names[$key])) {
        //         $names[$key] = $data['name'];
        //     }
        // }
        // $model->name = $data["name"];
        // $model->state_id = $data["state_id"];
        // $model->save();
    }
}
