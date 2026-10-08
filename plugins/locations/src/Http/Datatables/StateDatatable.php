<?php
namespace Progmix\Locations\Http\Datatables;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Juzaweb\CMS\Abstracts\DataTable;
use Progmix\Locations\Models\State;

class StateDatatable extends DataTable
{
    /**
     * Columns datatable
     *
     * @return array
     */
    public function columns()
    {
        return [
            'name'       => [
                'label' => trans('cms::app.name'),
                'formatter' => [$this, 'rowActionsFormatter'],

            ],
            'country_id' => [
                'label'     => trans('loc::content.country'),
                'formatter' => function ($value, $row, $index) {
                    return $row->country->name;
                },
            ],
            'iso_code'   => [
                'label' => trans('loc::content.iso_code'),
            ],
            'active'     => [
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
            'actions'    => [
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
        $query = State::query()->with('country');
        if ($keyword = Arr::get($data, 'keyword')) {
            $query->where(function (Builder $q) use ($keyword) {
                $q->where('name', JW_SQL_LIKE, '%' . $keyword . '%')
                    ->orWhereHas('country', function (Builder $q) use ($keyword) {
                        $q->where('countries.name', JW_SQL_LIKE, '%' . $keyword . '%');
                    });
            });
        }

        $configCountries = get_config('countries');
        if (! empty($configCountries)) {
            $query->whereIn('country_id', $configCountries);
        }
        return $query;
    }

    public function bulkActions($action, $ids)
    {
        global $jw_user;
        switch ($action) {
            case 'delete':
                foreach ($ids as $id) {
                    $model = State::find($id);
                    $model->delete();
                }
                break;
        }
    }
    public function rowActionsFormatter($value, $row, $index): string
    {
        $actions = $this->rowAction($row);
        $editUrl = $this->currentUrl . '/' . $row->id . '/edit';

        return view(
            'cms::backend.items.datatable_item',
            [
                'value'   => $value,
                'row'     => $row,
                'actions' => $actions,
                'editUrl' => $editUrl,
            ]
        )->render();
    }
}
