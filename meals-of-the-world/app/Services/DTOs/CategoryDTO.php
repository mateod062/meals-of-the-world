<?php

namespace App\Services\DTOs;

use App\Models\Category;

/**
 * DTO for the {@link Category} model
 */
class CategoryDTO
{
    public int $id;
    public string $slug;

    public function __construct(string $slug)
    {
        $this->slug = $slug;
    }

    public static function fromModel(Category $category): CategoryDTO
    {
        $categoryDTO = new CategoryDTO($category->slug);
        $categoryDTO->id = $category->id;

        return $categoryDTO;
    }
}
