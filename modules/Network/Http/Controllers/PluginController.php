<?php

namespace Juzaweb\Network\Http\Controllers;

use Illuminate\Contracts\View\View;
use Juzaweb\CMS\Http\Controllers\BackendController;

class PluginController extends BackendController
{
    public function index(): View
    {
        return view(
            'network::plugin.index',
            [
                'title' => trans_cms('cms::app.plugins'),
            ]
        );
    }

    public function install(): View
    {
        if (!config('juzaweb.plugin.enable_upload')) {
            abort(403, 'Access deny.');
        }

        $this->addBreadcrumb(
            [
                'url' => route('admin.plugin'),
                'title' => trans_cms('cms::app.plugins')
            ]
        );

        $title = trans_cms('cms::app.install');

        return view(
            'network::plugin.install',
            compact('title')
        );
    }
}
