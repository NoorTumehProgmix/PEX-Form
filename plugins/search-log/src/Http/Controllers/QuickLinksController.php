<?php

namespace Progmix\SearchLog\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Juzaweb\CMS\Http\Controllers\BackendController;
use Juzaweb\CMS\Traits\ResourceController;
use Maatwebsite\Excel\Facades\Excel;
use Progmix\SearchLog\Http\Datatables\SearchLogDatatable;
use Progmix\SearchLog\Models\SearchLog;
use Juzaweb\Backend\Exports\SearchLogSubmissionsExport;

class QuickLinksController extends BackendController
{
    use ResourceController {
        getDataForForm as DataForForm;
        afterSave as tAfterSave;
    }

    protected $viewPrefix = 'SearchLog::backend.quick-links';

    protected function getDataTable(...$params)
    {
        return new SearchLogDatatable();
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
        return SearchLog::class;
    }

    protected function getTitle(...$params)
    {
        return trans('SearchLog::content.name');
    }

    protected function getDataForForm($model, ...$params): array
    {
        $data = $this->DataForForm($model);
        return $data;
    }
}
