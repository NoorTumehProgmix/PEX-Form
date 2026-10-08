<?php

namespace Juzaweb\Applications\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Progmix\Donations\Models\Donation;

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

        Donation::truncate();
        Donation::create([
            'id' => 1,
            'name' => 'test',
            'email' => 'test@progmix.dev',
            'phone' => '1234567890',
            'payment_method' => 'Paypal',
            'payment_status' => 'Paid',
            'payment_id' => '1234567890',
            'payment_amount' => '100',
            'payment_currency' => 'USD',
            'recurring' => 'No',
            'recurring_amount' => '100',
            'recurring_interval' => 'Monthly',
            'note' => 'test',
        ]);

        // $this->call("OthersTableSeeder");
    }
}
