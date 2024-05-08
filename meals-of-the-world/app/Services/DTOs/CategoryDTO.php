<?php

namespace App\Services\DTOs;

use App\Models\Category;

class CategoryDTO
{
    public int $id;
    public string $title;
    public string $slug;

    public function __construct(Category $category, string $lang)
    {
        $this->id = $category->id;
        $this->title = $category->translations->where('language_code', $lang)->first()->title ?? '';
        $this->slug = $category->slug;
    }
}
