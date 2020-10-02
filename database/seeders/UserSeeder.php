<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        DB::table('users')->insert([
            'name' => $faker->name,
            'school_name' => $faker->company,
            'email' => $faker->safeEmail,
            'password' => Hash::make('password'),
            'created_at' => $faker->dateTimeBetween('-2 days'),
            'updated_at' => $faker->dateTimeBetween('-1 days')
        ]);
    }
}
