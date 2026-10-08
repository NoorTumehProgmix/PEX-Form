<?php

namespace Progmix\Redirections\Http\Datatables;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Juzaweb\CMS\Abstracts\DataTable;
use Progmix\Redirections\Models\Redirection;

class RedirectionDatatable extends DataTable
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
                'label' => trans('redirections::content.title'),
                'formatter' => [$this, 'rowActionsFormatter']
            ],
            'old_link' => [
                'label' => trans('redirections::content.old_link'),
            ],
            'new_link' => [
                'label' => trans('redirections::content.new_link'),
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
        $query = Redirection::query();

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
                Redirection::destroy($ids);
                break;
        }
    }
}
