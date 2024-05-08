<?php

namespace App\Services\DTOs;

use App\Models\Tag;

/**
 * DTO for the {@link Tag} model
 */
class TagDTO
{
    public int $id;
    public string $slug;

    public function __construct(string $slug)
    {
        $this->slug = $slug;
    }

    public static function fromModel(Tag $tag): TagDTO
    {
        $tagDTO = new TagDTO($tag->slug);
        $tagDTO->id = $tag->id;

        return $tagDTO;
    }
}
