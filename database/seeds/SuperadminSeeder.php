<?php

use Illuminate\Database\Seeder;
use App\Models\Superadmin;

class SuperadminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Superadmin::create([
            'firstname' => 'Super',
            'lastname' => 'Admin',
            'username' => 'Examplesuperadmin',
            'contact_number' => '987651238',
            'email' => 'Example.superadmin@aipxperts.com',
            'password' => bcrypt('aipX@123')
        ]);
    }
}
