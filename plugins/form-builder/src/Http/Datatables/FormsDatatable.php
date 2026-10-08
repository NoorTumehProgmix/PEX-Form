<?php

namespace Progmix\FormBuilder\Http\Datatables;

use Progmix\FormBuilder\Models\Form;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Juzaweb\Backend\Models\Post;
use Juzaweb\CMS\Abstracts\DataTable;
use Juzaweb\CMS\Facades\HookAction;

// use Nabilaissa\ContactUs\Models\Contact;

class FormsDatatable extends DataTable
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
                'label' => trans_cms('cms::app.name'),
                'formatter' => [$this, 'rowActionsFormatter'],
            ],
            'type' => [
                'label' => trans_cms('cms::app.type'),
                'formatter' => function ($value, $row, $index) {
                    return  trans_cms("formBuilder::content.$row->type");
                },
            ],
            'post_number' => [
                'label' => trans_cms('cms::app.post_number'),
                'sortable' => false,
                'formatter' => function ($value, $row, $index) {
                    return  Post::whereMeta('form', $row->id)?->count();
                },
            ],
            'created_at' => [
                'label' => trans_cms('cms::app.created_at'),
                'width' => '15%',
                'align' => 'center',
                'formatter' => function ($value, $row, $index) {
                    return jw_date_format($row->created_at);
                },
            ],
            'actions' => [
                'label' => trans('cms::app.actions'),
                'width' => '15%',
                'align' => 'center',
                'formatter' => function ($value, $row, $index) {
                    if ($row->submittable)
                        return '<a href="form-submissions?form=' . $row->id . '" class="btn btn-info px-2">' . trans('formBuilder::content.view_submissions') . '</a>';
                    else
                        return '';
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
        $query = Form::query();

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
                try {
                    Form::destroy($ids);
                } catch (\Exception $e) {
                    $data = [
                        "status" => false,
                        "message" => "Form has submissions please delete them first.",
                    ];
                    return $data;
                }
                break;
        }
    }
}
