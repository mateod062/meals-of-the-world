<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Language;
use App\Models\Translation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Log;

/**
 * @extends Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        return [
            'slug' => $this->faker->unique()->slug
        ];
    }

    /**
     * Configure the factory to automatically create translations for each category.
     *
     * @return Factory
     */
    public function configure(): Factory
    {
        return $this->afterCreating(function (Category $category) {
            $languages = Language::all();
            foreach ($languages as $language) {
                $existingTranslation = Translation::where([
                    'translatable_type' => Category::class,
                    'translatable_id' => $category->id,
                    'language_code' => $language->code,
                ])->exists();

                if (!$existingTranslation) {
                    Translation::factory()->create([
                        'translatable_type' => Category::class,
                        'translatable_id' => $category->id,
                        'language_code' => $language->code,
                        'title' => $this->faker->sentence,
                    ]);
                }
            }
        });
    }
}
