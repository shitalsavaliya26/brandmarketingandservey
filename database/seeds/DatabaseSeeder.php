<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        $this->call(AdminSeeder::class);
        $this->call(SuperadminSeeder::class);
        $this->call(PriceSettingSeeder::class);
        $this->call(CurrencySeeder::class);
    }
}
