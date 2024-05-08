<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Language;
use App\Models\Translation;
use Faker\Generator as Faker;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CategoriesTableSeeder extends Seeder
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
        Category::truncate();

        $languages = Language::all();

        Category::factory(20)->create()->each(function ($category) use ($languages) {
            foreach ($languages as $language) {
                Translation::factory()->create([
                    'translatable_type' => Category::class,
                    'translatable_id' => $category->id,
                    'language_code' => $language->code,
                    'title' => $this->faker->sentence
                ]);
            }
        });
    }
}
