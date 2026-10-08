<?php

namespace Progmix\Restrictions\Http\Datatables;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Juzaweb\CMS\Abstracts\DataTable;
use Progmix\Restrictions\Models\Restriction;

class RestrictionDatatable extends DataTable
{
    /**
     * Columns datatable
     *
     * @return array
     */
    public function columns()
    {
        return [
            'ip' => [
                'label' => trans('restrictions::content.ip'),
                'formatter' => [$this, 'rowActionsFormatter']
            ],
            'notes' => [
                'label' => trans('restrictions::content.notes'),
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
        $query = Restriction::query();

        if ($keyword = Arr::get($data, 'keyword')) {
            $query->where(function (Builder $q) use ($keyword) {
                $q->where('ip', 'LIKE', '%' . $keyword . '%')
                  ->orWhere('notes', 'LIKE', '%' . $keyword . '%');
            });
        }

        return $query;
    }

    public function bulkActions($action, $ids)
    {
        switch ($action) {
            case 'delete':
                Restriction::destroy($ids);
                break;
        }
    }
}
