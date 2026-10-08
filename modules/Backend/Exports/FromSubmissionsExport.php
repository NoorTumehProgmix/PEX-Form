<?php

namespace Juzaweb\Backend\Exports;

use Progmix\FormBuilder\Models\FormSubmission;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class FromSubmissionsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $form, $start_date, $end_date,$uniqueKeys;

    public function __construct($form, $start_date, $end_date)
    {
        $this->form = $form;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->uniqueKeys = $this->getUniqueKeys();
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        if ($this->form) {

            $query = FormSubmission::with('form:id,name')
                ->where('form_id', $this->form);
        } else {
            $query = FormSubmission::with('form:id,name');
        }
        // Filter by date range
        if ($this->start_date != "") {
            $query->where('created_at', '>=', $this->start_date);
        }

        if ($this->end_date != "") {
            $query->where('created_at', '<=', $this->end_date);
        }

        return $query->get(['form_id', 'form_data', 'created_at']);
    }

    public function map($row): array
    {
        $formData = json_decode($row->form_data, true);
        $data = isset($formData['data']) ? $formData['data'] : [];
        unset($data['submit']);

        $formFields = array_fill_keys($this->uniqueKeys, '');

        foreach ($data as $key => $value) {
            if (array_key_exists($key, $formFields)) {
                $formFields[$key] = is_array($value) ? json_encode($value) : $value;
            }
        }

        return array_merge(
            [$row->form->name],
            $formFields,
            [$row->created_at->format('Y-m-d H:i:s')]
        );
    }

    public function headings(): array
    {
        return array_merge(
            ['Form'],
            $this->uniqueKeys,
            ['Submission Date']
        );
    }

    protected function getUniqueKeys(): array
    {
        $formSubmissions = FormSubmission::get();
        $uniqueKeys = [];

        foreach ($formSubmissions as $formSubmission) {
            $formDataStructure = json_decode($formSubmission->form_data, true);
            $dataStructure = isset($formDataStructure['data']) ? $formDataStructure['data'] : [];
            unset($dataStructure['submit']);

            $keys = array_keys($dataStructure);
            $uniqueKeys = array_merge($uniqueKeys, $keys);
        }

        return array_unique($uniqueKeys);
    }
}
