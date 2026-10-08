<?php

namespace Juzaweb\Backend\Http\Controllers\Backend;

use Juzaweb\CMS\Http\Controllers\BackendController;
use Juzaweb\Backend\Http\Datatables\EmailLogDatatable;

class EmailLogController extends BackendController
{
    public function index(): \Illuminate\Contracts\View\View
    {
        global $jw_user;
        if (!$jw_user->can('email_logs.index')) {
            abort(403);
        }

        $dataTable = new EmailLogDatatable();
        $title = trans_cms('cms::app.email_logs');

        return view(
            'cms::backend.logs.email',
            compact(
                'title',
                'dataTable'
            )
        );
    }
}
