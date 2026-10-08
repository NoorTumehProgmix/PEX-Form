<?php

namespace Juzaweb\Backend\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Juzaweb\Backend\Models\SettingItem;
use Juzaweb\CMS\Http\Controllers\BackendController;
use Juzaweb\CMS\Traits\SettingItemTypeController;

class SettingItemController extends BackendController
{
    use SettingItemTypeController;

    protected string $viewPrefix = 'cms::backend.setting-item';

    protected function getModel(...$params): string
    {

        return SettingItem::class;
    }
}
