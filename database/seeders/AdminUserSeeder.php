<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Juzaweb\CMS\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // create user
        User::create([
            'name' => 'ProgmixSuperAdmin',
            'email' => 'admin@progmix.dev',
            'password' => bcrypt('h55XdscJ053vsADnJ8'),
            'is_admin' => 1,
        ]);
    }
}
