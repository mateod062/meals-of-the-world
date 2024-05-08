<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Language;
use App\Models\Meal;
use App\Models\Tag;
use App\Models\Translation;
use App\Repositories\MealRepository;
use App\Services\DTOs\MealDTO;
use App\Services\Impl\MealServiceImpl;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MealServiceTest extends TestCase
{
    use RefreshDatabase;

    private MealServiceImpl $mealService;
    private $mockMealRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockMealRepository = Mockery::mock(MealRepository::class);
        $this->mealService = new MealServiceImpl($this->mockMealRepository);
    }

    /**
     * @throws ValidationException
     */
    #[Test]
    public function test_getAllMeals_returns_paginated_meals(): void
    {
        $language = Language::factory()->create([
            'code' => 'en',
            'name' => 'English'
        ]);
        $meals = Meal::factory()->count(10)->create()->each(function ($meal) use ($language) {
            Translation::factory()->createOne([
                'translatable_type' => Meal::class,
                'translatable_id' => $meal->id,
                'language_code' => $language->code,
                'title' => 'Title',
                'description' => 'Description'
            ]);
        });

        $mockMealRepository = Mockery::mock(MealRepository::class);

        $mockMealRepository->shouldReceive('findAll')
            ->once()
            ->andReturn(new LengthAwarePaginator($meals, $meals->count(), 10));

        $mockMealService = new MealServiceImpl($mockMealRepository);

        $result = $mockMealService->getAllMeals([
            'lang' => 'en',
            'page' => 1,
            'per_page' => 10
        ]);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('meals', $result);
        $this->assertCount(10, $result['meals']);
    }

    #[Test]
    public function test_getMealById_returns_meal_dto(): void
    {
        $meal = Meal::factory()->create();
        $this->mockMealRepository->shouldReceive('findById')
            ->once()
            ->with($meal->id)
            ->andReturn($meal);

        $result = $this->mealService->getMealById($meal->id);

        $this->assertInstanceOf(MealDTO::class, $result);
        $this->assertEquals($meal->id, $result->id);
    }

    /**
     * @throws ValidationException
     */
    #[Test]
    public function test_createMeal_creates_and_returns_meal(): void
    {
        $category = Category::factory()->create();
        $tags = Tag::factory()->count(2)->create();
        $ingredients = Ingredient::factory()->count(3)->create();

        $data = [
            'category' => ['slug' => $category->slug],
            'tags' => $tags->pluck('slug')->toArray(),
            'ingredients' => $ingredients->pluck('slug')->toArray()
        ];

        $this->mockMealRepository->shouldReceive('save')
            ->once()
            ->andReturn(true);

        $result = $this->mealService->createMeal($data);

        $this->assertEquals($data, $result);
    }

    /**
     * @throws ValidationException
     */
    #[Test]
    public function test_updateMeal_updates_and_returns_meal(): void
    {
        $meal = Meal::factory()->create();
        $category = Category::factory()->create();
        $tags = Tag::factory()->count(2)->create();
        $ingredients = Ingredient::factory()->count(3)->create();

        $data = [
            'id' => $meal->id,
            'category' => ['slug' => $category->slug],
            'tags' => $tags->pluck('slug')->toArray(),
            'ingredients' => $ingredients->pluck('slug')->toArray()
        ];

        $this->mockMealRepository->shouldReceive('findById')
            ->once()
            ->with($meal->id)
            ->andReturn($meal);

        $this->mockMealRepository->shouldReceive('update')
            ->once()
            ->andReturn(true);

        $result = $this->mealService->updateMeal($meal->id, $data);

        $this->assertEquals($data, $result);
    }

    #[Test]
    public function test_deleteMeal_removes_meal_successfully(): void
    {
        $meal = Meal::factory()->create();

        $this->mockMealRepository->shouldReceive('findById')
            ->once()
            ->andReturn($meal);

        $this->mockMealRepository->shouldReceive('delete')
            ->once()
            ->andReturn(true);

        $this->mealService->deleteMeal($meal->id);

        $this->assertTrue(true);
    }
}

