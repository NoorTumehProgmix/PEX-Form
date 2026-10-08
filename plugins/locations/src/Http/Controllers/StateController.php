<?php

namespace Progmix\Locations\Http\Controllers;

use Juzaweb\CMS\Traits\ResourceController;
use Progmix\Locations\Http\Datatables\StateDatatable;
use Juzaweb\CMS\Http\Controllers\BackendController;
use Progmix\Locations\Models\State;
use Progmix\Locations\Models\Country;
use Illuminate\Support\Facades\Lang;


class StateController extends BackendController
{
    use ResourceController {
        getDataForForm as DataForForm;
    }
    protected $viewPrefix = 'loc::backend..state';

    protected function getDataTable(...$params)
    {
        return new StateDatatable();
    }
    protected function validator(array $attributes, ...$params)
    {
        return [
            'name' => ['required', 'string', 'max:100'],

        ];
    }

    protected function getModel(...$params)
    {
        return State::class;
    }
    protected function getTitle(...$params)
    {
        return trans('loc::content.states');
    }

    protected function getDataForForm($model, ...$params): array
    {
        $data = $this->DataForForm($model);
        $query = Country::query();
        $data['countries'] = $query->get();
        return $data;
    }
    protected function BeforeSave($data, $model, ...$params): void
    {
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
        // $model->name = $names;
        // $model->country_id = $data["country_id"];
        // $model->save();
    }
}
