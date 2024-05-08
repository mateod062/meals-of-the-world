<?php

namespace App\Services\DTOs;

use App\Models\Tag;

/**
 * Response DTO for the {@link Tag} model
 */
class TagResponseDTO extends TagDTO
{
    public string $title;

    public function __construct(Tag $tag, string $lang)
    {
        parent::__construct($tag->slug);

        $this->id = $tag->id;
        $this->title = $tag->translations->where('language_code', $lang)->first()->title ?? '';
        $this->slug = $tag->slug;
    }
}
