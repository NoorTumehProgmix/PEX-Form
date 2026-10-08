<?php

namespace Juzaweb\Backend\Exports;

use Juzaweb\Subscriptions\Models\Subscription;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SubscriptionExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Subscription::select('email', 'lang', 'created_at')->get();
    }

    public function map($row): array
    {
        return [
            $row->email,
            $row->lang,
            $row->created_at->format('Y-m-d H:i:s'),
        ];
    }
    public function headings(): array
    {
        return ['Email', 'Language', 'Subscription Date'];
    }
}
