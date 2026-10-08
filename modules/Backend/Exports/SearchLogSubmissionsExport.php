<?php

namespace Juzaweb\Backend\Exports;

use Progmix\FormBuilder\Models\FormSubmission;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Progmix\SearchLog\Models\SearchLog;

class SearchLogSubmissionsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $start_date, $end_date;

    public function __construct($start_date, $end_date)
    {
        $this->start_date = $start_date;
        $this->end_date   = $end_date;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = SearchLog::query();

        // Filter by date range
        if ($this->start_date != "") {
            $query->where('created_at', '>=', $this->start_date);
        }

        if ($this->end_date != "") {
            $query->where('created_at', '<=', $this->end_date);
        }

        return $query->get(['text', 'lang', 'ip_address', 'created_at']);
    }

    public function map($row): array
    {
        $data       = json_decode($row, true);
        $formFields = [];

        foreach ($data as $key => $value) {
            $formFields[$key] = is_array($value) ? json_encode($value) : $value;
        }
        return $formFields;
    }

    public function headings(): array
    {
        return ['Search Term', 'Language', 'IP Address', 'Created At'];
    }
}
