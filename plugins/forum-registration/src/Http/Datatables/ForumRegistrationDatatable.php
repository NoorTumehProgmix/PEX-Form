<?php

namespace Progmix\ForumRegistration\Http\Datatables;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Juzaweb\CMS\Abstracts\DataTable;
use Progmix\ForumRegistration\Models\ForumRegistration;

class ForumRegistrationDatatable extends DataTable
{
    public function columns()
    {
        return [
            'name' => [
                'label' => trans_cms('forum-registration::content.name'),
            ],
            'email' => [
                'label' => trans_cms('forum-registration::content.email'),
            ],
            'institution' => [
                'label' => trans_cms('forum-registration::content.institution'),
            ],
            'part_type' => [
                'label' => trans_cms('forum-registration::content.part_type'),
            ],
            'created_at' => [
                'label' => trans_cms('cms::app.date'),
                'formatter' => function ($value, $row, $index) {
                    return date_format(Carbon::parse($row->created_at), 'F j, Y, g:i a');
                },
            ],
            'actions' => [
                'label' => trans_cms('cms::app.actions'),
                'width' => '15%',
                'align' => 'center',
                'formatter' => function ($value, $row, $index) {
                    return '<a href="forum-registrations/' . $row->id . '/edit" class="btn btn-info"><i class="fa fa-edit m-0"></i></a>';
                },
            ],
        ];
    }

    public function query($data)
    {
        $query = ForumRegistration::query();

        if ($keyword = Arr::get($data, 'keyword')) {
            $query->where(function (Builder $q) use ($keyword) {
                $q->where('name', JW_SQL_LIKE, '%' . $keyword . '%')
                    ->orWhere('email', JW_SQL_LIKE, '%' . $keyword . '%')
                    ->orWhere('institution', JW_SQL_LIKE, '%' . $keyword . '%');
            });
        }

        return $query;
    }

    public function bulkActions($action, $ids)
    {
        switch ($action) {
            case 'delete':
                ForumRegistration::destroy($ids);
                break;
        }
    }
}
