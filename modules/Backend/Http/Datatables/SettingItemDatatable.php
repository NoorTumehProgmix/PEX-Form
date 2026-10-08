<?php

namespace Juzaweb\Backend\Http\Datatables;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Juzaweb\Backend\Models\Post;
use Juzaweb\Backend\Models\SettingItem;
use Juzaweb\CMS\Abstracts\DataTable;
use Juzaweb\CMS\Facades\HookAction;
use Juzaweb\CMS\Models\User;

class SettingItemDatatable extends DataTable
{

    public function columns(): array
    {
        $columns['name'] = [
            'label'     => trans_cms('cms::app.name'),
            'width'     => '10%',
            'formatter' => function ($value, $row, $index) {
                return jw_date_format($row->name);
            },
        ];
        $columns['status'] = [
            'label'     => trans_cms('cms::app.status'),
            'width'     => '10%',
            'formatter' => function ($value, $row, $index) {
                return jw_date_format($row->status);
            },
        ];

        $columns['type'] = [
            'label' => trans_cms('cms::app.type'),
            'width' => '10%',
            'formatter' => function ($value, $row, $index) {
                return jw_date_format($row->settingCategory->name);
            },
        ];

        return $columns;
    }

  /**
     * Query data datatable
     *
     * @param array $data
     * @return Builder
     */
    public function query($data)
    {
        $query = SettingItem::query();

        if ($keyword = Arr::get($data, 'keyword')) {
            $query->where(function (Builder $q) use ($keyword) {
                $q->where('name', JW_SQL_LIKE, '%' . $keyword . '%');
            });
        }

        return $query;
    }

    public function bulkActions($action, $ids)
    {
        switch ($action) {
            case 'delete':
                try{
                    SettingItem::destroy($ids);
            } catch (\Exception $e) {
                $data = [
                    "status" => false,
                    "message" => "SettingItem has submissions please delete them first.",
                ];
                return $data;
            }
                break;
        }
    }
}
