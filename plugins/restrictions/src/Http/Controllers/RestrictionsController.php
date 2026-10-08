<?php

namespace Progmix\Restrictions\Http\Controllers;

use Juzaweb\CMS\Traits\ResourceController;
use Progmix\Restrictions\Http\Datatables\RestrictionDatatable;
use Progmix\Restrictions\Models\Restriction;
use Juzaweb\CMS\Http\Controllers\BackendController;
use Illuminate\Support\Facades\Validator;

class RestrictionsController extends BackendController
{
    use ResourceController {
        getDataForForm as DataForForm;
        afterSave as tAfterSave;
    }
    protected $viewPrefix = 'restrictions::backend';

    protected function getDataTable(...$params)
    {
        return new RestrictionDatatable();
    }
    protected function validator(array $attributes, ...$params)
    {
        $validator = Validator::make($attributes, [
            // Rules
        ]);

        return $validator;
    }

    protected function getModel(...$params)
    {
        return Restriction::class;
    }
    protected function getTitle(...$params)
    {
        return trans('restrictions::content.name');
    }

    protected function getDataForForm($model, ...$params): array
    {
        $data = $this->DataForForm($model);
        return $data;
    }
}
