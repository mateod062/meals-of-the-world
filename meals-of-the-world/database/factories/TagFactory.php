<?php

namespace Database\Factories;

use App\Models\Language;
use App\Models\Tag;
use App\Models\Translation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tag>
 */
class TagFactory extends Factory
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
        return $this->afterCreating(function (Tag $tag) {
            $languages = Language::all();
            foreach ($languages as $language) {
                $existingTranslation = Translation::where([
                    'translatable_type' => Tag::class,
                    'translatable_id' => $tag->id,
                    'language_code' => $language->code,
                ])->exists();

                if (!$existingTranslation) {
                    Translation::factory()->create([
                        'translatable_type' => Tag::class,
                        'translatable_id' => $tag->id,
                        'language_code' => $language->code,
                        'title' => $this->faker->sentence,
                    ]);
                }
            }
        });
    }
}
