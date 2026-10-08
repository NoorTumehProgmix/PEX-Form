<?php

namespace Progmix\Locations\Http\Datatables;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Juzaweb\CMS\Abstracts\DataTable;
use Progmix\Locations\Models\Country;

class CountryDatatable extends DataTable
{
    /**
     * Columns datatable
     *
     * @return array
     */
    public function columns()
    {
        return [
            'name'      => [
                'label' => trans('cms::app.name'),
                'formatter' => [$this, 'rowActionsFormatter'],

            ],
            'code'      => [
                'label' => trans('loc::content.iso_code'),
            ],
            'phonecode' => [
                'label' => trans('loc::content.phone_code'),
            ],
            'active'    => [
                'label'     => trans('cms::app.status'),
                'width'     => '10%',
                'align'     => 'center',
                'formatter' => function ($value, $row, $index) {
                    return view(
                        'cms::components.datatable.active',
                        compact('row')
                    )->render();
                },
            ],
            'actions'   => [
                'label'     => trans_cms('cms::app.actions'),
                'width'     => '10%',
                'align'     => 'center',
                'sortable'  => false,
                'formatter' => function ($value, $row, $index) {
                    return view(
                        'cms::components.datatable.actions',
                        [
                            'row'     => $row,
                            'actions' => $this->rowAction($row),
                        ]
                    )->render();
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
        $query = Country::query();
        if ($keyword = Arr::get($data, 'keyword')) {
            $query->where(function (Builder $q) use ($keyword) {
                $keyword = urldecode($keyword);
                $keyword = mb_strtolower($keyword);
                $q->where('name', JW_SQL_LIKE, '%' . ucfirst($keyword) . '%');
            });
        }
        $configCountries = get_config('countries');
        if (! empty($configCountries)) {
            $query->whereIn('id', $configCountries);
        }
        return $query;
    }

    public function bulkActions($action, $ids)
    {
        switch ($action) {
            case 'delete':
                foreach ($ids as $id) {
                    $model = Country::find($id);
                    $model->delete();
                }
                break;
        }
    }
}
