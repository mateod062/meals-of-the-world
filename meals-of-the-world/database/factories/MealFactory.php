<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Language;
use App\Models\Meal;
use App\Models\Translation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Meal>
 */
class MealFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::factory()
        ];
    }

    /**
     * Configure the factory to automatically create translations for each meal.
     *
     * @return Factory
     */
    public function configure(): Factory
    {
        return $this->afterCreating(function (Meal $meal) {
            $languages = Language::all();
            foreach ($languages as $language) {
                $existingTranslation = Translation::where([
                    'translatable_type' => Meal::class,
                    'translatable_id' => $meal->id,
                    'language_code' => $language->code,
                ])->exists();

                if (!$existingTranslation) {
                    Translation::factory()->create([
                        'translatable_type' => Meal::class,
                        'translatable_id' => $meal->id,
                        'language_code' => $language->code,
                        'title' => $this->faker->sentence,
                        'description' => $this->faker->paragraph
                    ]);
                }
            }
        });
    }
}
