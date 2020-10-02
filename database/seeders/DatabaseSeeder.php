<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(LinkSeeder::class);
        $this->call(AdminSeeder::class);
        $this->call(UserSeeder::class);
    }
}
