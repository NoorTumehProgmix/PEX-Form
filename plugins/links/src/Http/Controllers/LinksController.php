<?php

namespace Progmix\Links\Http\Controllers;

use Juzaweb\CMS\Traits\ResourceController;
use Progmix\Links\Http\Datatables\LinkDatatable;
use Progmix\Links\Models\Link;
use Juzaweb\CMS\Http\Controllers\BackendController;
use Illuminate\Support\Facades\Validator;

class LinksController extends BackendController
{
    use ResourceController {
        getDataForForm as DataForForm;
        afterSave as tAfterSave;
    }
    protected $viewPrefix = 'links::backend';

    protected function getDataTable(...$params)
    {
        return new LinkDatatable();
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
        return Link::class;
    }
    protected function getTitle(...$params)
    {
        return trans('links::content.name');
    }

    protected function getDataForForm($model, ...$params): array
    {
        $data = $this->DataForForm($model);
        return $data;
    }
}
