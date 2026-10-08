<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Juzaweb\CMS\Models\Language;
class LanguagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
    
        Language::create([
            'id' => 1,
            'code' => 'ar',
            'name' => 'Arabic',
            'default' => 1,
        ]);

        Language::create([
            'id' => 2,
            'code' => 'en',
            'name' => 'English',
            'default' => 0,
        ]);
    }
}
