<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            LanguagesTableSeeder::class,
            CategoriesTableSeeder::class,
            IngredientsTableSeeder::class,
            TagsTableSeeder::class,
            MealsTableSeeder::class
        ]);
    }
}
