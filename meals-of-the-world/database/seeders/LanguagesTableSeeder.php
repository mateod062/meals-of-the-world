<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\Translation;
use Illuminate\Database\Seeder;

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
