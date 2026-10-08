<?php

namespace Juzaweb\Backend\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Juzaweb\Backend\Models\Menu;
use Juzaweb\Backend\Models\MenuItem;
use Juzaweb\Backend\Models\Language;
use Juzaweb\CMS\Facades\GlobalData;
use Juzaweb\CMS\Facades\HookAction;
use Juzaweb\CMS\Http\Controllers\BackendController;
use Illuminate\Support\Facades\Lang;

class MenuController extends BackendController
{
    public function index($id = null)
    {
        global $jw_user;
        if (!$jw_user->can('menus.index')) {
            abort(403);
        }
        do_action('backend.menu.index', $id);


        $currentLang = Lang::locale();
        $title = trans_cms('cms::app.menu');
        $navMenus = get_config('nav_menus');
        $location = get_theme_config("nav_location_$currentLang");

        add_action('juzaweb.add_menu_items', [$this, 'addMenuBoxs']);

        $langs = Language::orderBy('default', 'desc')->get();

        if (empty($id)) {
            $menu = Menu::where('lang', $currentLang)->first();
        } else {
            $menu = Menu::where('id', '=', $id)
                ->where('lang', $currentLang)
                ->first();
            if (empty($menu)) {
                $menu = Menu::where('lang', $currentLang)->first();
            }
        }
        return view(
            'cms::backend.menu.index',
            compact(
                'title',
                'menu',
                'navMenus',
                'location',
                'langs'
            )
        );
    }

    public function addItem(Request $request)
    {
        global $jw_user;
        if (!$jw_user->can('menus.edit')) {
            abort(403);
        }

        $request->validate(
            [
                'key' => 'required',
            ],
            [],
            [
                'key' => trans_cms('cms::app.key'),
            ]
        );

        $menuRegister = HookAction::getMenuBox($request->post('key'));

        if (empty($menuRegister)) {
            return $this->error(
                [
                    'message' => 'Cannot find menu box',
                ]
            );
        }

        $menuBox = $menuRegister->get('menu_box');

        $result = [];
        $data = $menuBox->mapData($request->all());

        foreach ($data as $item) {
            $model = new MenuItem();
            $model->fill(
                array_merge(
                    $item,
                    [
                        'box_key' => $request->post('key'),
                    ]
                )
            );

            $result[] = view(
                'cms::backend.items.menu_item',
                [
                    'item' => $model,
                ]
            )->render();
        }

        return $this->success(
            [
                'items' => $result,
            ]
        );
    }

    public function store(Request $request)
    {
        global $jw_user;
        if (!$jw_user->can('menus.create')) {
            abort(403);
        }
        $request->validate(
            [
                'name' => 'required|string|max:250',
            ],
            [],
            [
                'name' => trans_cms('cms::app.name'),
            ]
        );

        $model = Menu::create($request->all());
        $table_name = $model->getTable();

        $content = [
            'method' => "POST",
            'table' => $table_name,
            'id' => $model->id,
            'type' => $table_name,
            'label' => "added a new " . Str::singular($table_name),
            'title' => $model->name,
            'path' => "",
        ];
        log_action($content);
        return $this->success(
            [
                'message' => trans_cms('cms::app.saved_successfully'),
                'redirect' => route('admin.menu.id', [$model->id]),
            ]
        );
    }

    public function update(Request $request, $id)
    {
        global $jw_user;
        if (!$jw_user->can('menus.edit')) {
            abort(403);
        }
        $request->validate(
            [
                'name' => 'required|string|max:150',
                'content' => 'required',
            ],
            [],
            [
                'name' => trans_cms('cms::app.name'),
                'content' => trans_cms('cms::app.menu'),
            ]
        );

        $items = json_decode($request->post('content'), true);
        DB::beginTransaction();

        try {
            $model = Menu::findOrFail($id);
            $model->update($request->all());
            $model->syncItems($items);
            $menu_lang = $model->lang;
            $currentConfig = get_theme_config("nav_location_$menu_lang");

            if ($location = $request->post('location', [])) {
                if ($currentConfig) {
                    foreach ($currentConfig as $key => $value) {
                        if ($value == $model->id) {
                            unset($currentConfig[$key]);
                        }
                    }
                }

                foreach ($location as $item) {
                    $currentConfig[$item] = $model->id;
                }
                set_theme_config("nav_location_$menu_lang", $currentConfig);
            } else {
                $location = collect(get_theme_config('nav_location'))
                    ->filter(
                        function ($i) use ($model) {
                            return $i != $model->id;
                        }
                    )->toArray();

                if (!empty($location)) {
                    set_theme_config("nav_location_$menu_lang", $location);
                }
            }

            do_action('admin.saved_menu', $model, $items);
            $table_name = $model->getTable();
            $content = [
                'method' => "PUT",
                'table' => $table_name,
                'id' => $model->id,
                'type' => $table_name,
                'label' => "updated " . Str::singular($table_name),
                'title' => $model->name,
                'path' => "",
                'form_data' => $request->all(),
            ];
            log_action($content);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            throw $e;
        }

        return $this->success(
            [
                'message' => trans_cms('cms::app.saved_successfully'),
                'redirect' => route('admin.menu.id', [$model->id]),
            ]
        );
    }

    public function destroy($id)
    {
        global $jw_user;
        if (!$jw_user->can('menus.delete')) {
            abort(403);
        }

        $menu = Menu::findOrFail($id);

        $menu->delete();
        $content = [
            'method' => "DELETE",
            'table' => "menus",
            'id' => $menu->id,
            'type' => "menus",
            'label' => "deleted a menu",
            'title' => $menu->name,
            'path' => "",
        ];
        log_action($content);
        return $this->success(
            [
                'message' => trans_cms('cms::app.deleted_successfully'),
            ]
        );
    }

    public function addMenuBoxs()
    {
        $menuBoxs = GlobalData::get('menu_boxs');

        foreach ($menuBoxs as $key => $item) {
            echo e(
                view(
                    'cms::backend.items.menu_box',
                    [
                        'label' => $item['title'],
                        'key' => $key,
                        'slot' => $item['menu_box']->addView()->render(),
                    ]
                )
            );
        }
    }
}
