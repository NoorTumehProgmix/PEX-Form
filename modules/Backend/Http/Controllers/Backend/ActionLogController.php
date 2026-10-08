<?php

namespace Juzaweb\Backend\Http\Controllers\Backend;

use Juzaweb\CMS\Http\Controllers\BackendController;
use Juzaweb\Backend\Http\Datatables\ActionLogDatatable;

class ActionLogController extends BackendController
{
    public function index(): \Illuminate\Contracts\View\View
    {
        global $jw_user;
        if (!$jw_user->can('actions_logs.index')) {
            abort(403);
        }

        $dataTable = new ActionLogDatatable();
        $title = trans_cms('cms::app.actions_logs');

        return view(
            'cms::backend.logs.actions',
            compact(
                'title',
                'dataTable'
            )
        );
    }
}
