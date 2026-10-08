<?php

namespace Juzaweb\Backend\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Juzaweb\Backend\Exports\BranchExport;
use Juzaweb\Backend\Imports\BranchImport;
use Juzaweb\CMS\Http\Controllers\BackendController;
use Juzaweb\Backend\Models\Post;
use Juzaweb\CMS\Traits\PostTypeController;
use Maatwebsite\Excel\Facades\Excel;

class PostController extends BackendController
{
    use PostTypeController;

    protected string $viewPrefix = 'cms::backend.post';

    protected function getModel(...$params): string
    {

        return Post::class;
    }

    public function import(Request $request, $type): string
    {

        if ($type == 'branches') {
            Excel::import(new BranchImport, $request->file);
        }

        return back()->with('success',  trans_cms('cms::app.success'));
    }

    public function export(Request $request, $type)
    {
        if ($type == 'branches') {
            return Excel::download(new BranchExport(), 'branches.xlsx');
        }

        return back()->with('message', trans_cms('cms::app.invalid_post_type'));
    }
}
