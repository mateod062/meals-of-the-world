<?php

namespace App\Services\DTOs;

use App\Models\Ingredient;

/**
 * DTO for the {@link Ingredient} model
 */
class IngredientDTO
{
    public int $id;
    public string $slug;

    public function __construct(string $slug)
    {
        $this->slug = $slug;
    }

    public static function fromModel(Ingredient $ingredient): IngredientDTO
    {
        $ingredientDTO = new IngredientDTO($ingredient->slug);
        $ingredientDTO->id = $ingredient->id;

        return $ingredientDTO;
    }
}
