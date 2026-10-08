<?php

namespace Progmix\Api\Http\Datatables;

use Progmix\Api\Models\ApiLog;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Juzaweb\CMS\Abstracts\DataTable;

class ApiLogsDatatable extends DataTable
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
                'align' => 'center',
                'formatter' => function ($value, $row, $index) {
                    return  view(
                        'cms::backend.items.datatable_item',
                        [
                            'value' => ucwords(str_replace('-', ' ',   $row->api->name)),
                            'row' => $row,
                            'actions' => $this->rowAction($row),
                            'editUrl' =>route('apiLogs.view',$row->id),
                            'editUrlShow' =>false,

                        ]
                    )
                        ->render();
                },
            ],
            'attempt_id' => [
                'label' => trans_cms('api::content.attempt_num'),
                'align' => 'center',
                'formatter' => function ($value, $row, $index) {
                    return $row->attempt_id;
                }
            ],
            'request' => [
                'label' => trans_cms('api::content.request'),
                'align' => 'center',
                'formatter' => function ($value, $row, $index) {
                    return $row->request;
                }
            ],
            'response' => [
                'label' => trans_cms('api::content.response'),
                'align' => 'center',
                'formatter' => function ($value, $row, $index) {
                    return  substr($row->response, 0, 80) . '...';
                }
            ],
            'type' => [
                'label' => trans_cms('cms::app.type'),
                'align' => 'center',
                'formatter' => function ($value, $row, $index) {
                    return $row->type;
                }
            ],
            'ip' => [
                'label' => trans_cms('api::content.sender_ip'),
                'align' => 'center',
                'formatter' => function ($value, $row, $index) {
                    return $row->ip;
                }
            ],
            'status_code' => [
                'label' => trans_cms('api::content.status_code'),
                'align' => 'center',
                'formatter' => function ($value, $row, $index) {
                    return $row->status_code;
                }
            ],
            'actions' => [
                'label' => trans('cms::app.actions'),
                'width' => '15%',
                'align' => 'center',
                'formatter' => function ($value, $row, $index) {
                    return '<a href="' . route('apiLogs.view', $row->id) . '" class="btn btn-info px-2"><i class="fa fa-search"></i></a>';
                },
            ],

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
        $query = ApiLog::query();

        if ($keyword = Arr::get($data, 'keyword')) {
            $query->whereHas('api', function (Builder $q) use ($keyword) {
                $q->where('name', 'like', '%' . $keyword . '%');
            });
        }

        if ($keyword = Arr::get($data, 'api')) {
            $query->whereHas('api', function (Builder $q) use ($keyword) {
                $q->where('id', $keyword);
            });
        }

        return $query;
    }

    public function bulkActions($action, $ids)
    {
        switch ($action) {
            case 'delete':
                ApiLog::destroy($ids);
                break;
        }
    }
    public function rowAction($row): array
    {
        $data = parent::rowAction($row);
        $data['view'] = [
            'label' => trans_cms('cms::app.view'),
            'url' =>route('apiLogs.view',$row->id),
        ];
        return $data;
    }
    public function searchFields(): array
    {
        $data = [
            'keyword' => [
                'type' => 'text',
                'label' => trans_cms('cms::app.keyword'),
                'placeholder' => trans_cms('cms::app.keyword'),
            ],
            'api' => [
                'type' => 'select',
                'width' => '100px',
                'label' => trans_cms('cms::app.api'),
                'options' => $forms = $this->makeModel()->get()->pluck('name', 'id')->toArray(),
            ],
        ];
        return $data;
    }

    protected function makeModel()
    {

        return app('Progmix\Api\Models\Api');
    }
}
