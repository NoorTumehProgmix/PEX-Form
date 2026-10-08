<?php

namespace Juzaweb\Backend\Http\Datatables;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;
use Juzaweb\CMS\Abstracts\DataTable;
use Juzaweb\Backend\Models\Role;

class RoleDatatable extends DataTable
{
    /**
     * Columns datatable
     *
     * @return array
     */
    public function columns()
    {
        return [
            'name' => [
                'label' => trans_cms('cms::app.name'),
                'formatter' => [$this, 'rowActionsFormatter'],
            ],
            'guard_name' => [
                'label' => trans_cms('cms::app.guard_name'),
            ],
            'created_at' => [
                'label' => trans_cms('cms::app.created_at'),
                'width' => '15%',
                'align' => 'center',
                'formatter' => function ($value, $row, $index) {
                    return jw_date_format($row->created_at);
                }
            ]
        ];
    }

    /**
     * Query data datatable
     *
     * @param array $data
     * @return Builder
     */
    public function query($data)
    {
        $query = Role::query();

        if ($keyword = Arr::get($data, 'keyword')) {
            $query->where(
                function (Builder $q) use ($keyword) {
                    // $q->where('title', JW_SQL_LIKE, '%'. $keyword .'%');
                }
            );
        }

        return $query;
    }

    public function bulkActions($action, $ids)
    {
        // switch ($action) {
        //     case 'delete':
        //         Role::destroy($ids);
        //         break;
        // }

        foreach ($ids as $id) {
            switch ($action) {
                case 'delete':
                    $role = Role::find($id);
                    Role::destroy($id);
                    $content = [
                        'method' => $action,
                        'table' => "roles",
                        'id' => $id,
                        'type' => "roles",
                        'label' => "deleted a role",
                        'title' => $role->name,
                        'path' => "",
                    ];
                    log_action($content);
                    break;
            }
        }
    }
}
