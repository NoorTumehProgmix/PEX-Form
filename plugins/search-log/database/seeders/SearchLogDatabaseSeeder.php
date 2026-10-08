<?php

namespace Progmix\SearchLog\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Juzaweb\Backend\Models\Language;
use Juzaweb\Backend\Models\Menu;

class SearchLogDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $languages = Language::all();
        foreach ($languages as $lang) {
            Menu::create(
                [
                    'name' => 'Quick Links',
                    'type' => 'quick-links',
                    'lang' => $lang,
                ]
            );
        }
    }
}
