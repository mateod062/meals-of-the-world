<?php

namespace App\Services\DTOs;

use App\Models\Meal;

/**
 * DTO for the {@link Meal} model
 */
class MealDTO
{
    public int $id;
    public CategoryDTO $category;
    public array $tags;
    public array $ingredients;

    public function __construct(string $categorySlug, array $tags, array $ingredients)
    {
        $this->category = new CategoryDTO($categorySlug);
        $this->tags = array_map(fn($slug) => new TagDTO($slug), $tags);
        $this->ingredients = array_map(fn($slug) => new IngredientDTO($slug), $ingredients);
    }

    public static function fromModel(Meal $meal): MealDTO
    {
        $mealDTO = new MealDTO(
            $meal->category->slug,
            $meal->tags->pluck('slug')->toArray(),
            $meal->ingredients->pluck('slug')->toArray()
        );
        $mealDTO->id = $meal->id;

        return $mealDTO;
    }
}
