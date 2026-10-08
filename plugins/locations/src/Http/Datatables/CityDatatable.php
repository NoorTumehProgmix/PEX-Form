<?php
namespace Progmix\Locations\Http\Datatables;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Juzaweb\CMS\Abstracts\DataTable;
use Progmix\Locations\Models\City;

class CityDatatable extends DataTable
{
    /**
     * Columns datatable
     *
     * @return array
     */
    public function columns()
    {
        return [
            'name'     => [
                'label' => trans('cms::app.name'),
                'formatter' => [$this, 'rowActionsFormatter'],
            ],
            'state_id' => [
                'label'     => trans('loc::content.state'),
                'formatter' => function ($value, $row, $index) {
                    return $row->state->name;
                },
            ],
            'actions'  => [
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
        $query = City::query();

        if ($search = Arr::get($data, 'keyword')) {
            $keyword = urldecode($search);
            $keyword = mb_strtolower($keyword);
            $query->where(function (Builder $q) use ($keyword, $search) {
                $q->whereRaw('LOWER(name) LIKE ?', ['%' . $keyword . '%'])
                    ->orWhere('name', 'LIKE', '%' . mb_strtolower($search) . '%');
            });
        }

        $configCountries = get_config('countries');
        if (! empty($configCountries)) {
            $query->whereHas('state', function (Builder $q) use ($configCountries) {
                $q->whereIn('country_id', $configCountries);
            });
        }

        return $query;
    }

    public function bulkActions($action, $ids)
    {
        global $jw_user;
        switch ($action) {
            case 'delete':
                foreach ($ids as $id) {
                    $model = City::find($id);
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
