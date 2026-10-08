<?php

namespace Progmix\ContactUs\Http\Datatables;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Juzaweb\CMS\Abstracts\DataTable;
use Progmix\ContactUs\Models\Contact;

class ContactUsDatatable extends DataTable
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
                'label' => trans_cms('contact::content.name'),
            ],
            'email'      => [
                'label' => trans_cms('contact::content.email'),
            ],
            'phone'      => [
                'label' => trans_cms('contact::content.phone'),
            ],
            'subject'      => [
                'label' => trans_cms('contact::content.subject'),
            ],
            'created_at' => [
                'label'     => trans_cms('cms::app.date'),
                'formatter' => function ($value, $row, $index) {
                    return date_format(Carbon::parse($row->created_at), 'F j, Y, g:i a');
                },
            ],
            'actions'    => [
                'label'     => trans_cms('cms::app.actions'),
                'width'     => '15%',
                'align'     => 'center',
                'formatter' => function ($value, $row, $index) {
                    return '<a href="contact-us/' . $row->id . '/edit" class="btn btn-info"><i class="fa fa-edit m-0"></i></a>';
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
        $query = Contact::query();

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
                Contact::destroy($ids);
                break;
        }
    }
}
