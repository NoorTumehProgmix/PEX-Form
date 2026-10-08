<?php

namespace Progmix\Api\Http\Datatables;

use Progmix\Api\Models\Api;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Juzaweb\CMS\Abstracts\DataTable;

class ApisDatatable extends DataTable
{
    /**
     * Columns datatable
     *
     * @return array
     */
    public function columns()
    {
        return [
            'name'    => [
                'label'     => 'Name',
                'width'     => '15%',
                'align'     => 'start',
                'formatter' => function ($value, $row, $index) {
                    return  view(
                        'cms::backend.items.datatable_item',
                        [
                            'value' => ucwords(str_replace('-', ' ',   $row->name)),
                            'row' => $row,
                            'actions' => $this->rowAction($row),
                            'editUrl' => route('api.edit', $row->id),
                        ]
                    )
                        ->render();
                },

            ],

            'edge_url'    => [
                'label'     => trans_cms('api::content.edge_url'),
                'width'     => '15%',
                'align'     => 'start',
                'formatter' => function ($value, $row, $index) {
                    return  config('app.url') . str_replace('{progmix_slug}', $row->slug, $row->edge_url);
                },

            ],
            'origin_url'    => [
                'label'     => trans_cms('api::content.origin_url'),
                'width'     => '15%',
                'align'     => 'start',
                'formatter' => function ($value, $row, $index) {
                    return str_replace('-', ' ', $row->origin_url);
                },
            ],
            'status'    => [
                'label'     => trans_cms('cms::app.status'),
                'width'     => '15%',
                'align'     => 'start',
                'formatter' => function ($value, $row, $index) {
                    $row['active'] = $value;
                    return view(
                        'cms::components.datatable.active',
                        compact('row')
                    )->render();
                }
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
        $query = Api::query();

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
                Api::destroy($ids);
            } catch (\Exception $e) {
                $data = [
                    "status" => false,
                    "message" => "API has Logs please delete them first.",
                ];
                return $data;
            }
                break;
        }
    }
}
