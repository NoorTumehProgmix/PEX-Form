<?php

namespace Progmix\SearchLog\Http\Datatables;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Juzaweb\CMS\Abstracts\DataTable;
use Progmix\SearchLog\Models\SearchLog;

class SearchLogDatatable extends DataTable
{
    /**
     * Columns datatable
     *
     * @return array
     */
    public function columns()
    {
        return [
            'text'       => [
                'label' => trans('SearchLog::content.text'),
            ],
            'lang'       => [
                'label' => trans('cms::app.lang'),
            ],
            'ip_address' => [
                'label' => trans('SearchLog::content.ip_address'),
            ],
            'created_at' => [
                'label'     => trans('cms::app.date'),
                'align'     => 'center',
                'formatter' => function ($value, $row, $index) {
                    return jw_date_format($row->created_at);
                },
            ],
            'actions'    => [
                'label'     => trans('cms::app.actions'),
                'width'     => '15%',
                'align'     => 'center',
                'formatter' => function ($value, $row, $index) {
                    return '<a href="search-log/' . $row->id . '/edit" class="btn btn-info px-2"><i class="fa fa-eye m-0"></i></a>';
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
        $query = SearchLog::query();

        if ($keyword = Arr::get($data, 'keyword')) {
            $query->where(function (Builder $q) use ($keyword) {
                $q->where('text', JW_SQL_LIKE, '%' . $keyword . '%');
            });
        }

        // Filter by date range
        if (isset($data['start_date'])) {
            $query->where('created_at', '>=', $data['start_date']);
        }

        if (isset($data['end_date'])) {
            $query->where('created_at', '<=', $data['end_date']);
        }

        return $query;
    }

    public function bulkActions($action, $ids)
    {
        switch ($action) {
            case 'delete':
                SearchLog::destroy($ids);
                break;
        }
    }

    public function searchFields(): array
    {
        $data = [
            'keyword'    => [
                'type'        => 'text',
                'sub_type'    => "text",
                'width'       => '100px',
                'label'       => trans_cms('cms::app.keyword'),
                'placeholder' => trans_cms('cms::app.keyword'),
            ],
            'start_date' => [
                'type'        => 'text',
                'sub_type'    => "date",
                'width'       => '100px',
                'label'       => trans_cms('cms::app.start_date'),
                'placeholder' => trans_cms('cms::app.start_date'),
            ],
            'end_date'   => [
                'type'        => 'text',
                'sub_type'    => "date",
                'width'       => '100px',
                'label'       => trans_cms('cms::app.end_date'),
                'placeholder' => trans_cms('cms::app.end_date'),
            ],
        ];

        return $data;
    }

    protected function makeModel()
    {
        return app('Progmix\SearchLog\Models\SearchLog');
    }
}
