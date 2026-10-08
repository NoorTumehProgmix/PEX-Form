<?php

namespace Progmix\Redirections\Http\Controllers;

use Juzaweb\CMS\Traits\ResourceController;
use Progmix\Redirections\Http\Datatables\RedirectionDatatable;
use Progmix\Redirections\Models\Redirection;
use Juzaweb\CMS\Http\Controllers\BackendController;
use Illuminate\Support\Facades\Validator;

class RedirectionsController extends BackendController
{
    use ResourceController {
        getDataForForm as DataForForm;
        afterSave as tAfterSave;
    }
    protected $viewPrefix = 'redirections::backend';

    protected function getDataTable(...$params)
    {
        return new RedirectionDatatable();
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
        return Redirection::class;
    }
    protected function getTitle(...$params)
    {
        return trans('redirections::content.name');
    }

    protected function getDataForForm($model, ...$params): array
    {
        $data = $this->DataForForm($model);
        return $data;
    }
}
