<?php


namespace Juzaweb\Backend\Observers;

use Juzaweb\Backend\Models\Menu;
use Illuminate\Support\Facades\Cache;

class MenuObserver
{
    public function saved(Menu $menu): void
    {
        Cache::store('file')->pull(cache_prefix("menu_items_menu_{$menu->id}_"));
    }

    public function deleted(Menu $menu): void
    {
        Cache::store('file')->pull(cache_prefix("menu_items_menu_{$menu->id}"));
    }
}
