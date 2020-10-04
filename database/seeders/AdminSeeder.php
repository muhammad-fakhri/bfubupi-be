<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $faker = Faker::create();

        // DB::table('admins')->insert([
        //     'name' => $faker->name,
        //     'email' => $faker->safeEmail,
        //     'password' => Hash::make('password'),
        //     'is_super_admin' => true,
        //     'created_at' => $faker->dateTimeBetween('-2 days'),
        //     'updated_at' => $faker->dateTimeBetween('-1 days')
        // ]);

        // Admin::factory()->count(2)->create();

        DB::table('admins')->insert([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password123'),
            'is_super_admin' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}
