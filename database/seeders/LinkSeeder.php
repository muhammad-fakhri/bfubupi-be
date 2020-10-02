<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('links')->insert([
            ['code' => 'regis_lktin', 'value' => 'https://drive.google.com'],
            ['code' => 'regis_micro', 'value' => 'https://drive.google.com'],
            ['code' => 'regis_olim_sains', 'value' => 'https://drive.google.com'],
            ['code' => 'regis_olim_bio', 'value' => 'https://drive.google.com'],
            ['code' => 'regis_lct_bio', 'value' => 'https://drive.google.com'],
            ['code' => 'upload_payment', 'value' => 'https://drive.google.com'],
            ['code' => 'upload_paper', 'value' => 'https://drive.google.com'],
            ['code' => 'rulebook_lktin', 'value' => 'https://drive.google.com'],
            ['code' => 'rulebook_micro', 'value' => 'https://drive.google.com'],
            ['code' => 'rulebook_olim_sains', 'value' => 'https://drive.google.com'],
            ['code' => 'rulebook_olim_bio', 'value' => 'https://drive.google.com'],
            ['code' => 'rulebook_lct_bio', 'value' => 'https://drive.google.com'],
            ['code' => 'timeline', 'value' => 'https://drive.google.com'],
            ['code' => 'bank_soal', 'value' => 'https://drive.google.com'],
            ['code' => 'template', 'value' => 'https://drive.google.com'],
        ]);
    }
}
