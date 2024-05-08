<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use App\Models\Language;
use App\Models\Translation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;
use Illuminate\Support\Facades\DB;

class IngredientsTableSeeder extends Seeder
{
    protected $faker;

    public function __construct(Faker $faker) {
        $this->faker = $faker;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Ingredient::truncate();

        $languages = Language::all();

        Ingredient::factory(50)->create()->each(function ($tag) use ($languages) {
            foreach ($languages as $language) {
                Translation::factory()->create([
                    'translatable_type' => Ingredient::class,
                    'translatable_id' => $tag->id,
                    'language_code' => $language->code,
                    'title' => $this->faker->sentence
                ]);
            }
        });
    }
}
