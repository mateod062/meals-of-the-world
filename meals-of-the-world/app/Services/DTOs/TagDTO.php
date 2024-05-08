<?php

namespace App\Services\DTOs;

use App\Models\Tag;

class TagDTO
{
    public int $id;
    public string $title;
    public string $slug;

    public function __construct(Tag $tag, string $lang)
    {
        $this->id = $tag->id;
        $this->title = $tag->translations->where('language_code', $lang)->first()->title ?? '';
        $this->slug = $tag->slug;
    }
}
