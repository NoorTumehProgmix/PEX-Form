<?php

namespace Juzaweb\Applications\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Juzaweb\Subscriptions\Models\Subscription;

class ApplicationsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        Subscription::truncate();

        Subscription::create([
            'id' => 1,
            'email' => 'test@progmix.dev',
            'lang' => 'en',
        ]);

        // $this->call("OthersTableSeeder");
    }
}
