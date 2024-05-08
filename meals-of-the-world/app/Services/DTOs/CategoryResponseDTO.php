<?php

namespace App\Services\DTOs;

use App\Models\Category;

/**
 * Response DTO for the {@link Category} model
 */
class CategoryResponseDTO extends CategoryDTO
{
    public string $title;

    public function __construct(Category $category, string $lang)
    {
        parent::__construct($category->slug);

        $this->id = $category->id;
        $this->title = $category->translations->where('language_code', $lang)->first()->title ?? '';
        $this->slug = $category->slug;
    }
}
