<?php

namespace Progmix\PaymentMethods\Http\Datatables;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Juzaweb\CMS\Abstracts\DataTable;
use Progmix\PaymentMethods\Models\PaymentMethod;

class PaymentMethodsDatatable extends DataTable
{
    /**
     * Columns datatable
     *
     * @return array
     */
    public function columns(): array
    {
        return [
            'name' => [
                'label' => trans('paymentMethods::content.name'),
                'formatter' => [$this, 'rowActionsFormatter'],
            ],
            'type' => [
                'label' => trans('paymentMethods::content.method'),
                'width' => '20%',
                'formatter' => function ($value, $row, $index) {
                    return trans("paymentMethods::content.data.payment_methods.{$value}");
                }
            ],
            'active' => [
                'label' => trans('cms::app.status'),
                'width' => '10%',
                'align' => 'center',
                'formatter' => function ($value, $row, $index) {
                    return view(
                        'cms::components.datatable.active',
                        compact('row')
                    )->render();
                }
            ],
            'created_at' => [
                'label' => trans('cms::app.created_at'),
                'width' => '20%',
                'align' => 'center',
                'formatter' => function ($value, $row, $index) {
                    return jw_date_format($row->created_at);
                }
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
     * @param  array  $data
     * @return Builder
     */
    public function query(array $data): \Illuminate\Contracts\Database\Query\Builder
    {
        $query = PaymentMethod::select(
            [
                'id',
                'name',
                'type',
                'active',
                'created_at'
            ]
        );

        if ($keyword = Arr::get($data, 'keyword')) {
            $query->where(function (Builder $q) use ($keyword) {
                $q->where('name', JW_SQL_LIKE, '%'. $keyword .'%');
            });
        }

        if ($type = Arr::get($data, 'type')) {
            $query->where('type', '=', $type);
        }

        return $query;
    }

    public function bulkActions($action, $ids): void
    {
        switch ($action) {
            case 'delete':
                PaymentMethod::destroy($ids);
                break;
        }
    }
}
