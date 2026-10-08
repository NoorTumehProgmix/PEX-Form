<?php

namespace Juzaweb\Subscriptions\Http\Datatables;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Juzaweb\Subscriptions\Models\Subscription;
use Juzaweb\Backend\Models\Language;
use Juzaweb\CMS\Abstracts\DataTable;
use Illuminate\Support\Facades\Gate;

class SubscriptionDatatable extends DataTable
{
    /**
     * Columns datatable
     *
     * @return array
     */
    public function columns()
    {
        return [
            'email' => [
                'label' => trans('subscriptions::content.email'),
                'formatter' => [$this, 'rowActionsFormatter'],
            ],
            'lang' => [
                'label' => trans('subscriptions::content.lang'),
                'formatter' => function ($value, $row, $index) {
                    if (isset($value)) {
                        $language = Language::whereCode($value)->first();
                        if ($language) {
                            return $language->name;
                        }
                    }
                },
            ],
            'created_at' => [
                'label' => trans('subscriptions::content.subscribed_at'),
                'width' => '15%',
                'align' => 'center',
                'formatter' => function ($value, $row, $index) {
                    return jw_date_format($row->created_at);
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
        $query = Subscription::query();

        if ($keyword = Arr::get($data, 'keyword')) {
            $query->where(function (Builder $q) use ($keyword) {
                $q->where('email', JW_SQL_LIKE, '%' . $keyword . '%');
            });
        }

        return $query;
    }

    public function bulkActions($action, $ids)
    {
        switch ($action) {
            case 'delete':
                if (Gate::allows('Subscriptions.delete')) {
                    $del_ids = isset($ids) && !empty($ids) ? $ids : $_POST['ids'];
                    $items = Subscription::whereIn('id', $del_ids)->get();
                    foreach ($items as $item) {
                        $content = [
                            'method' => "DELETE",
                            'table' => "Subscriptions",
                            'id' => $item->id,
                            'type' => "",
                            'label' => "deleted a subscription",
                            'title' => $item->email,
                            'path' => "",
                        ];
                        log_action($content);
                    }
                    Subscription::destroy($del_ids);
                }
                break;
        }
    }
}
