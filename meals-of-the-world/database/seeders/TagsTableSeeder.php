<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\Tag;
use App\Models\Translation;
use Faker\Generator as Faker;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TagsTableSeeder extends Seeder
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
        Tag::truncate();

        $languages = Language::all();

        Tag::factory(30)->create()->each(function ($tag) use ($languages) {
            foreach ($languages as $language) {
                Translation::factory()->create([
                    'translatable_type' => Tag::class,
                    'translatable_id' => $tag->id,
                    'language_code' => $language->code,
                    'title' => $this->faker->sentence
                ]);
            }
        });

        /*foreach ($tags as $tag) {
            $translationsData = [];

            foreach ($languages as $language) {
                $translationsData[] = [
                    'translatable_type' => Tag::class,
                    'translatable_id' => $tag->id,
                    'language_code' => $language->code,
                    'title' => $this->faker->word
                ];
            }

            Translation::upsert(
                $translationsData,
                ['translatable_type', 'translatable_id', 'language_code'],
                ['title']
            );
        }*/
    }
}
