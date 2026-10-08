<?php

namespace Juzaweb\Backend\Http\Controllers\Backend;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Juzaweb\Backend\Http\Datatables\RoleDatatable;
use Juzaweb\Backend\Models\Post;
use Juzaweb\Backend\Models\Role;
use Juzaweb\CMS\Abstracts\Action;
use Juzaweb\CMS\Abstracts\DataTable;
use Juzaweb\CMS\Facades\HookAction;
use Juzaweb\CMS\Http\Controllers\BackendController;
use Juzaweb\CMS\Models\Permission;
use Juzaweb\CMS\Traits\ResourceController;

class RoleController extends BackendController
{
    use ResourceController {
        getDataForForm as DataForForm;
        afterSave as tAfterSave;
    }

    protected string $viewPrefix = 'cms::backend.role';

    public function __construct()
    {
        do_action(Action::PERMISSION_INIT);
    }

    protected function getDataTable(...$params): DataTable
    {
        return new RoleDatatable();
    }

    protected function validator(array $attributes, ...$params): \Illuminate\Contracts\Validation\Validator
    {
        $permissions = HookAction::getPermissions()
            ->pluck('name')
            ->toArray();

        return Validator::make(
            $attributes,
            [
                'name' => 'required|string|max:100',
                'description' => 'nullable|string|max:200',
                'permissions' => 'nullable|array',
                // 'permissions.*' => [
                //     'nullable',
                //     Rule::in($permissions)
                // ],
            ]
        );
    }

    protected function afterSave($data, Role $model, ...$params)
    {
        $current_permissions = json_decode($data['current_permissions']);
        $permissions = Arr::get($data, 'permissions', []);

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission],
                ['description' => 'page permissions']
            );
        }

        $exists = Permission::whereIn('name', $permissions)
            ->get(['name'])
            ->pluck('name')
            ->toArray();

        $permissionData = HookAction::getPermissions()
            ->whereIn(
                'name',
                collect($permissions)
                    ->filter(
                        function ($item) use ($exists) {
                            return !in_array($item, $exists);
                        }
                    )
                    ->toArray()
            );

        foreach ($permissionData as $item) {
            Permission::create(
                [
                    'name' => $item['name'],
                    'description' => $item['description'],
                ]
            );
        }


        $g_permissions = HookAction::getPermissions();
        $curGroupPermissionsIDS = [];
        foreach ($g_permissions as $key => $item) {
            $g_permission = Permission::where("name", $item['name'])->first();
            if ($g_permission) {
                $curGroupPermissionsIDS[] = $g_permission->id;
            }
        }
        $model->syncPermissionsWithCurrentPage($permissions, $current_permissions, $curGroupPermissionsIDS);
    }

    protected function getDataForForm($model, ...$params): array
    {
        $data = $this->DataForForm($model);
        $data['groups'] = $this->getPermissionGroups();
        $pagesData = $this->getPages();
        $data['sections'] = $pagesData["pages"];
        $curPagePermissions = [];
        $curPagePermissionsIDS = [];

        foreach ($data['sections'] as $section) {
            $curPagePermissions[] = 'view.' . $section['id'];
            $curPagePermissions[] = 'edit.' . $section['id'];
            $curPagePermissions[] = 'delete.' . $section['id'];
            $curPagePermissions[] = 'add.' . $section['id'];
            $curPagePermissions[] = 'edit.posts.' . $section['id'];
            $curPagePermissions[] = 'delete.posts.' . $section['id'];
        }
        foreach ($curPagePermissions as $per) {
            $permission = Permission::where("name", $per)->first();
            if ($permission) {
                $curPagePermissionsIDS[] = $permission->id;
            }
        }
        $data['pages_permissions'] = $pagesData["permissions"];
        $data['cur_pages_permissions'] = json_encode($curPagePermissionsIDS);
        return $data;
    }

    protected function getModel(...$params): string
    {
        return Role::class;
    }

    protected function getTitle(...$params): string
    {
        return trans_cms('cms::app.roles');
    }

    protected function getPermissionGroups(): \Illuminate\Support\Collection
    {
        $permissions = HookAction::getPermissions();
        $groups = HookAction::getPermissionGroups();

        foreach ($permissions as $key => $item) {
            if ($group = $item->get('group')) {
                $group = $groups->get($group);
                $pers = $group->get('permissions', []);
                $pers[$key] = $item;
                $group->put('permissions', $pers);
                $groups[$group->get('key')] = $group;
            }
        }

        return $groups;
    }

    protected function getPages()
    {
        $pages = Post::where("type", "pages")
            ->where("lang", '=', get_config('language') ? get_config('language') : 'en')
            ->where('status', '!=', Post::STATUS_PREVIEW)
            ->paginate(10);

        $permissions = HookAction::getPermissionGroups("post-type__pages");
        $data = [
            "pages" => $pages,
            "permissions" => $permissions['permissions'] ?? [],
        ];

        return $data;
    }
}
