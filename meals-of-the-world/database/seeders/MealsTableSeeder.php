<?php

namespace Database\Seeders;

use App\Models\Meal;
use Illuminate\Database\Seeder;

class MealsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Meal::truncate();

        Meal::factory(10)->hasIngredients(5)->hasTags(3)->create();
    }
}
