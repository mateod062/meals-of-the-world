<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\Translation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LanguagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Translation::truncate();
        Language::truncate();

        Language::factory(10)->create();
    }
}
