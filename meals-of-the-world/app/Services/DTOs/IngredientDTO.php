<?php

namespace App\Services\DTOs;

use App\Models\Ingredient;

class IngredientDTO
{
    public int $id;
    public string $title;
    public string $slug;

    public function __construct(Ingredient $ingredient, string $lang)
    {
        $this->id = $ingredient->id;
        $this->title = $ingredient->translations->where('language_code', $lang)->first()->title ?? '';
        $this->slug = $ingredient->slug;
    }
}
