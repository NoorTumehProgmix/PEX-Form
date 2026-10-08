<?php

namespace Juzaweb\Backend\Imports;

use Carbon\Carbon;
use DB;
use Juzaweb\Backend\Models\Post;
use Juzaweb\Backend\Models\Taxonomy;
use Maatwebsite\Excel\Concerns\ToModel;

class BranchImport implements ToModel
{
    private $rowCount = 0;


    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $this->rowCount++;

        // Skip processing if it's the first row
        if ($this->rowCount === 1) {
            return null;
        }
        $arabicData = [
            'title' => $row[1],
            'subtitle' => null,
            'content' => null,
            'text' => null,
            'status' => 'publish',
            'views' => 0,
            'thumbnail' => null,
            'images' => null,
            'lang' => 'ar',
            'rel_id' => null,
            'slug' => $row[1] . '-ar',
            'oldslug' => null,
            'type' => 'branches',
            'json_metas' => [],
            'json_taxonomies' => [],
            'rating' => 0,
            'total_rating' => 0,
            'meta_title' => null,
            'meta_description' => null,
            'meta_keywords' => null,
            'date' => null,
            'end_date' => null,
            'published_at' => null,
            'path' => null,
            'show_sitemap' => 0,
            'files' => null,
            'display_order' => 100,
            'external_link' => null,
            'latlng' =>  $row[6] . ',' . $row[7],
        ];

        $englishData = [
            'title' => $row[2],
            'subtitle' => null,
            'content' => null,
            'text' => null,
            'status' => 'publish',
            'views' => 0,
            'thumbnail' => null,
            'images' => null,
            'lang' => 'en',
            'rel_id' => $row[0],
            'slug' => $row[1] . '-en',
            'oldslug' => null,
            'type' => 'branches',
            'json_metas' => [],
            'json_taxonomies' => [],
            'rating' => 0,
            'total_rating' => 0,
            'meta_title' => null,
            'meta_description' => null,
            'meta_keywords' => null,
            'date' => null,
            'end_date' => null,
            'published_at' => null,
            'path' => null,
            'show_sitemap' => 0,
            'files' => null,
            'display_order' => 100,
            'external_link' => null,
            'latlng' =>  $row[6] . ',' . $row[7],
        ];
        DB::beginTransaction();

        try {
            $arabicPost = Post::updateOrCreate(['id' => $row[0], 'lang' => 'ar'], $arabicData);
            $arabicPost->json_metas = [];
            $arabicPost->syncMetas($this->prepareJsonMetas('ar', $row));
            $arabicPost->syncTaxonomies($this->prepareJsonTaxonomies('ar', $row));

            $englishData['rel_id'] =  $arabicPost->id;
            $enId = Post::where('rel_id', $arabicPost->id)->where('lang', 'en')->first()?->id;

            $englishPost = Post::updateOrCreate(['id' => $enId, 'lang' => 'en'], $englishData);
            $englishPost->json_metas = [];

            $englishPost->syncMetas($this->prepareJsonMetas('en', $row));
            $englishPost->syncTaxonomies($this->prepareJsonTaxonomies('en', $row));

            DB::commit();
        } catch (\Exception $e) {
            dd($e->getMessage());

            DB::rollBack();
            return $e->getMessage();
        }

        return $arabicPost;
    }

    private function prepareJsonMetas(string $lang, array $row): array
    {
        $address = $lang == 'ar' ? $row[3] : $row[4];
        return  [
            'all_days_start' => Carbon::createFromTimestamp($row[17] * 86400)->format('H:i'),
            'all_days_end' => Carbon::createFromTimestamp($row[18] * 86400)->format('H:i'),
            'friday_start' => Carbon::createFromTimestamp($row[21] * 86400)->format('H:i'),
            'friday_end' => Carbon::createFromTimestamp($row[22] * 86400)->format('H:i'),
            'saturday_start' => Carbon::createFromTimestamp($row[23] * 86400)->format('H:i'),
            'saturday_end' => Carbon::createFromTimestamp($row[24] * 86400)->format('H:i'),
            'thursday_start' => Carbon::createFromTimestamp($row[19] * 86400)->format('H:i'),
            'thursday_end' => Carbon::createFromTimestamp($row[20] * 86400)->format('H:i'),
            'code' => $row[5],
            'phone' => $row[8],
            'email' => $row[9],
            'address_text' => $address,
        ];
    }

    private function prepareJsonTaxonomies(string $lang, array $row): array
    {
        $taxonomy = [
            "branch_type" => [
                0 =>  $lang == 'ar' ? (int)$row[10] : (int)Taxonomy::where('rel_id', $row[10])->where('lang', 'en')->first()?->id
            ],
            "city" =>  [
                0 => $lang == 'ar' ? (int)$row[11] : (int)Taxonomy::where('rel_id', $row[11])->where('lang', 'en')->first()?->id
            ],
            "status_group" =>  [
                0 =>  $lang == 'ar' ? (int)$row[25] : (int)Taxonomy::where('rel_id', $row[25])->where('lang', 'en')->first()?->id
            ],

        ];

        $filter = array();

        if ($row[12] != 0) {
            $filter[] = $lang == 'ar' ? Taxonomy::CAN_DEPOSIT_AR : Taxonomy::CAN_DEPOSIT_ER;
        }
        if ($row[13] != 0) {
            $filter[] = $lang == 'ar' ? Taxonomy::CORPORATE_CENTER_AR : Taxonomy::CORPORATE_CENTER_EN;
        }
        if ($row[14] != 0) {
            $filter[] = $lang == 'ar' ? Taxonomy::BILL_PAYMENT_AR : Taxonomy::BILL_PAYMENT_EN;
        }
        if ($row[15] != 0) {
            $filter[] = $lang == 'ar' ? Taxonomy::OPEN_SAT_AR : Taxonomy::OPEN_SAT_EN;
        }
        if ($row[16] != 0) {
            $filter[] = $lang == 'ar' ?  Taxonomy::HOURS_AR : Taxonomy::HOURS_EN;
        }

        $taxonomy['filter'] = $filter;

        return $taxonomy;
    }
}
