<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\Meal;
use App\Models\MealTranslation;
use App\Models\Translation;
use Faker\Generator as Faker;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MealsTableSeeder extends Seeder
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
        Meal::truncate();

        $languages = Language::all();

        Meal::factory(50)->hasIngredients(5)->hasTags(3)->create()->each(function ($meal) use ($languages) {
            foreach ($languages as $language) {
                Translation::factory()->create([
                    'translatable_type' => Meal::class,
                    'translatable_id' => $meal->id,
                    'language_code' => $language->code,
                    'title' => $this->faker->sentence,
                    'description' => $this->faker->paragraph
                ]);
            }
        });
    }
}
