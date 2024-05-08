<?php

namespace App\Services\DTOs;

use App\Models\Ingredient;

/**
 * Response DTO for the {@link Ingredient} model
 */
class IngredientResponseDTO extends IngredientDTO
{
    public string $title;

    public function __construct(Ingredient $ingredient, string $lang)
    {
        parent::__construct($ingredient->slug);

        $this->id = $ingredient->id;
        $this->title = $ingredient->translations->where('language_code', $lang)->first()->title ?? '';
        $this->slug = $ingredient->slug;
    }
}
