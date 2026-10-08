<?php

namespace Juzaweb\Applications\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Progmix\ContactUs\Models\Contact;

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

        Contact::truncate();
        Contact::create([
            'id' => 1,
            'name' => 'test',
            'email' => 'test@progmix.dev',
            'phone' => '1234567890',
            'message' => 'test',
            'subject' => 'test',
        ]);

        // $this->call("OthersTableSeeder");
    }
}
