<?php

namespace Juzaweb\Backend\Exports;

use Juzaweb\Backend\Models\Language;
use Juzaweb\Backend\Models\Post;
use Juzaweb\Backend\Models\Taxonomy;
use Maatwebsite\Excel\Concerns\FromCollection;

class BranchExport implements FromCollection
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $posts = Post::where('type','branches')->get();

        $exportData = [];
        $exportData[] = [
            0 => "id",
            1 => "Name Ar",
            2 => "Name En",
            3 => "Address Ar",
            4 => "Address En",
            5 => "code",
            6 => "lat",
            7 => "lng",
            8 => "phone",
            9 => "email",
            10 => "type",
            11 => "city_id",
            12 => "can_deposit",
            13 => "corporate_center",
            14 => "bill_payment",
            15 => "open_sat",
            16 => "24_hours",
            17 => "Sunday_wednesday_start",
            18 => "sunday_wednesday_end",
            19 => "Thursday_start",
            20 => "Thursday_end",
            21 => "Friday_start",
            22 => "Friday_end",
            23 => "saturday_start",
            24 => "saturday_end",
            25 => "status_group"
        ];
        $types =  Taxonomy::where('taxonomy', 'branch_type')->pluck('id');
        $cities =  Taxonomy::where('taxonomy', 'city')->pluck('id');
        $status_group =  Taxonomy::where('taxonomy', 'status_group')->pluck('id');

        foreach ($posts as $post) {
            if ($post->lang != Language::where('default', 1)?->get()?->first()?->code) {
                continue;
            }
            $taxonomy = collect($post->json_taxonomies);
            $exportData[] = [
                0 => $post->id ?? '-',
                1 => $post->title ?? '-',
                2 => $post->where('rel_id', $post->id)->where('lang', 'en')->first()?->title ?? '-',
                3 => $post->json_metas['address_text'] ?? '-',
                4 => $post->where('rel_id', $post->id)->where('lang', 'en')->first()?->json_metas['address_text'] ?? '-',
                5 => $post->json_metas['code'] ?? '-',
                6 => explode(",", $post->latlng)[0] ?? '-',
                7 => explode(",", $post->latlng)[1] ?? '-',
                8 => $post->json_metas['phone'] ?? '-',
                9 => $post->json_metas['email'] ?? '-',
                10 => $taxonomy->whereIn('id', $types)->first()['id'] ?? null,
                11 => $taxonomy->whereIn('id', $cities)->first()['id'] ?? null,
                12 => $taxonomy->whereIn('id', [8, 7])->first() != null ? 1 : '0',
                13 => $taxonomy->whereIn('id', [90, 91])->first() != null ? 1 : '0',
                14 => $taxonomy->whereIn('id', [10, 9])->first() != null ? 1 : '0',
                15 => $taxonomy->whereIn('id', [12, 11])->first() != null ? 1 : '0',
                16 => $taxonomy->whereIn('id', [16, 15])->first() != null ? 1 : '0',
                17 => $post->json_metas['all_days_start'] ?? '-',
                18 => $post->json_metas['all_days_end'] ?? '-',
                19 => $post->json_metas['thursday_start'] ?? '-',
                20 => $post->json_metas['thursday_end'] ?? '-',
                21 => $post->json_metas['friday_start'] ?? '-',
                22 => $post->json_metas['friday_end'] ?? '-',
                23 => $post->json_metas['saturday_start'] ?? '-',
                24 => $post->json_metas['saturday_end'] ?? '-',
                25 => $taxonomy->whereIn('id', $status_group)->first()['id'] ?? null,
            ];
        }

        return collect($exportData);
    }
}
