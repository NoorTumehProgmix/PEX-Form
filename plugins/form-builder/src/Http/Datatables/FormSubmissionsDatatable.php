<?php

namespace Progmix\FormBuilder\Http\Datatables;

use Progmix\FormBuilder\Models\FormSubmission;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Juzaweb\CMS\Abstracts\DataTable;

class FormSubmissionsDatatable extends DataTable
{
    /**
     * Columns datatable
     *
     * @return array
     */
    public function columns()
    {
        return [
            'name'    => [
                'label'     => trans('formBuilder::content.form_name'),
                'width'     => '15%',
                'align'     => 'start',
                'formatter' => [$this, 'rowActionsFormatter'],
            ],
            'created_at' => [
                'label' => trans('formBuilder::content.submitted_at'),
                'width' => '15%',
                'align' => 'center',
                'formatter' => function ($value, $row, $index) {
                    return jw_date_format($row->created_at);
                }
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
        $query = FormSubmission::query()->with('form');

        if ($keyword = Arr::get($data, 'keyword')) {
            $query->whereHas('form', function (Builder $q) use ($keyword) {
                $q->where('name', 'like', '%' . $keyword . '%');
            });
        }

        if ($keyword = Arr::get($data, 'formsSelect')) {
            $query->whereHas('form', function (Builder $q) use ($keyword) {
                $q->where('id', $keyword);
            });
        }

        // Filter by date range
        if (isset($data['start_date'])) {
            $query->where('created_at', '>=', $data['start_date']);
        }

        if (isset($data['end_date'])) {
            $query->where('created_at', '<=', $data['end_date']);
        }
        return $query;
    }

    public function bulkActions($action, $ids)
    {
        switch ($action) {
            case 'delete':
                FormSubmission::destroy($ids);
                break;
        }
    }

    public function rowActionsFormatter($value, $row, $index): string
    {
        $lang = app()->getLocale();

        $formData = json_decode($row['form_data'], true);
        $data = $formData['data'];
        unset($data['submit']);

        return view(
            'formBuilder::backend.submissions.show',
            [
                'data' => $data,
                'formSubmission' => $row,
                'lang' => $lang,
                'title' => $row->form->name
            ]
        )
            ->render();
    }


    public function rowAction($row): array
    {
        $data = parent::rowAction($row);
        $data['view'] = [
            'label' => trans_cms('cms::app.view'),
            'url' => route('form.view.disabled', $row->id),
            'target' => '_blank',
        ];
        return $data;
    }

    public function searchFields(): array
    {
        $data = [
            'formsSelect' => [
                'type' => 'select',
                'width' => '100px',
                'label' => trans_cms('cms::app.forms'),
                'options' => $this->makeModel()->get()->pluck('name', 'id')->toArray(),
                'selected' => @$_GET['form'],
            ],
            'start_date' => [
                'type' => 'text',
                'sub_type' => "date",
                'width' => '100px',
                'label' => trans_cms('cms::app.start_date'),
            ],
            'end_date' => [
                'type' => 'text',
                'sub_type' => "date",
                'width' => '100px',
                'label' => trans_cms('cms::app.end_date'),
            ],
        ];


        return $data;
    }

    protected function makeModel()
    {
        return app('Progmix\FormBuilder\Models\Form');
    }
}
