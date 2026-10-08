<?php

namespace Progmix\Api\Http\Controllers;

use Progmix\Api\Http\Datatables\ApiLogsDatatable;
use Progmix\Api\Models\ApiLog;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\Validator;
use Juzaweb\CMS\Http\Controllers\BackendController;
use Juzaweb\CMS\Traits\ResourceController;
use Illuminate\View\View;
use Juzaweb\Backend\Models\Language;

class ApiLogController extends BackendController
{

    use ResourceController {
        getDataForForm as DataForForm;
        afterSave as tAfterSave;
    }
    protected $viewPrefix = 'api::api-log';


    protected function getDataTable(...$params)
    {
        return new ApiLogsDatatable();
    }

    protected function validator(array $attributes, ...$params)
    {
        return true;
    }

    protected function getModel(...$params)
    {
        return ApiLog::class;
    }

    protected function getTitle(...$params)
    {
        return 'API Log';
    }
    protected function getDataForForm($model, ...$params): array
    {
        $data = $this->DataForForm($model);
        return $data;
    }

    public function view(ApiLog $apiLog)
    {
        $title = 'view Api logs';
        return view('api::api-log.view', compact('apiLog', 'title'));
    }
}
