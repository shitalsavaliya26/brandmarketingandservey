<?php

use Illuminate\Database\Seeder;
use App\Models\Admin;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Admin::create([
            'organization_name' => 'Example subscriber',
            'firstname' => 'Example',
            'lastname' => 'subscriber',
            'contact_number' => '1234567890',
            'email' => 'Example.subscriber@aipxperts.com',
            'password' => bcrypt('example@123'),
            'website_url' => 'https://www.example.com/',
            'country_id' => 101,
        ]);
    }
}
