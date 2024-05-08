<?php

namespace Tests\Feature;

use App\Models\Ingredient;
use App\Models\Language;
use App\Models\Meal;
use App\Models\Tag;
use App\Models\Translation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MealControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function index_returns_meals(): void
    {
        $language = Language::factory()->createOne([
            'code' => 'en',
            'name' => 'English'
        ]);

        $meals = Meal::factory()->count(10)->create()->each(function ($meal) use ($language) {
            Translation::factory()->makeOne([
                'translatable_type' => Meal::class,
                'translatable_id' => $meal->id,
                'language_code' => $language->code,
                'title' => 'Title',
                'description' => 'Description'
            ]);
        });

        $response = $this->getJson('/api/meals?lang=en');

        $response->assertOk()
            ->assertJsonStructure([
                'meta' => [
                    'currentPage',
                    'totalItems',
                    'itemsPerPage',
                    'totalPages'
                ],
                'data' => [
                    '*' => [
                        'id', 'title', 'description', 'status'
                    ]
                ]
            ]);
    }

    #[Test]
    public function index_returns_meals_with_ingredients_and_tags(): void
    {
        $language = Language::factory()->createOne([
            'code' => 'en',
            'name' => 'English'
        ]);

        $tags = Tag::factory()->count(3)->create()->each(function ($tag) use ($language) {
            Translation::factory()->createOne([
                'translatable_type' => Tag::class,
                'translatable_id' => $tag->id,
                'language_code' => $language->code,
                'title' => 'Title'
            ]);
        });

        $ingredients = Ingredient::factory()->count(5)->create()->each(function ($ingredient) use ($language) {
            Translation::factory()->createOne([
                'translatable_type' => Ingredient::class,
                'translatable_id' => $ingredient->id,
                'language_code' => $language->code,
                'title' => 'Title',
            ]);

            $meals = Meal::factory()->count(10)->hasIngredients(5)->hasTags(3)->create()->each(function ($meal) use ($language) {
                Translation::factory()->createOne([
                    'translatable_type' => Meal::class,
                    'translatable_id' => $meal->id,
                    'language_code' => $language->code,
                    'title' => 'Title',
                    'description' => 'Description'
                ]);
            });

            $response = $this->getJson('/api/meals?lang=en&with=tags,ingredients');

            $response->assertOk()
                ->assertJsonPath('data.0.status', 'created')
                ->assertJsonStructure([
                    'meta' => [
                        'currentPage',
                        'totalItems',
                        'itemsPerPage',
                        'totalPages'
                    ],
                    'data' => [
                        '*' => [
                            'id', 'title', 'description', 'status', 'tags', 'ingredients'
                        ]
                    ]
                ]);
        });
    }

    #[Test]
    public function index_correctly_paginates_meals(): void
    {
        $language = Language::factory()->createOne([
            'code' => 'en',
            'name' => 'English'
        ]);

        $totalMeals = 50; // Total number of meals to test pagination
        $perPage = 10;    // Meals per page
        Meal::factory()->count($totalMeals)->create()->each(function ($meal) use ($language) {
            Translation::factory()->createOne([
                'translatable_type' => Meal::class,
                'translatable_id' => $meal->id,
                'language_code' => $language->code,
                'title' => 'Title',
                'description' => 'Description'
            ]);
        });

        $page = 1; // Start with the first page
        while ($page <= ceil($totalMeals / $perPage)) {
            $response = $this->getJson("/api/meals?lang=en&per_page=$perPage&page=$page");

            $response->assertOk()
                ->assertJsonStructure([
                    'meta' => [
                        'currentPage',
                        'totalItems',
                        'itemsPerPage',
                        'totalPages'
                    ],
                    'data' => [
                        '*' => [
                            'id', 'title', 'description', 'status'
                        ]
                    ]
                ])
                ->assertJson([
                    'meta' => [
                        'currentPage' => $page,
                        'totalItems' => $totalMeals,
                        'itemsPerPage' => $perPage,
                        'totalPages' => ceil($totalMeals / $perPage)
                    ]
                ]);

            $dataCount = count($response->json('data'));
            $expectedCount = $perPage;
            if ($page === ceil($totalMeals / $perPage)) {
                $expectedCount = $totalMeals % $perPage ?: $perPage;
            }
            $this->assertEquals($expectedCount, $dataCount, "Page $page should have $expectedCount items");

            $page++; //Move to the next page
        }
    }
}
