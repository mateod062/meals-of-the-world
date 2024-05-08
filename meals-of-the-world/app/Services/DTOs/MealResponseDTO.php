<?php

namespace App\Services\DTOs;

use App\Models\Meal;
use JsonSerializable;

/**
 * Response DTO for the {@link Meal} model
 */
class MealResponseDTO extends MealDTO implements JsonSerializable
{
    public string $title;
    public string $description;
    public string $status;
    private string $with;

    public function __construct(Meal $meal, string $lang, $with = '', $diff_time = null)
    {
        parent::__construct(
            $meal->category->slug,
            $meal->tags->pluck('slug')->toArray(),
            $meal->ingredients->pluck('slug')->toArray()
        );

        $this->id = $meal->id;
        $this->title = $meal->translations->where('language_code', $lang)->first()->title ?? '';
        $this->description = $meal->translations->where('language_code', $lang)->first()->description ?? '';
        $this->status = $this->determineStatus($meal, $diff_time);

        // Load requested relations from the with request parameter
        $relations = explode(',', $with);
        if (in_array('category', $relations) && $meal->relationLoaded('category')) {
            $this->category = new CategoryResponseDTO($meal->category, $lang);
        }
        if (in_array('tags', $relations) && $meal->relationLoaded('tags')) {
            $this->tags = $meal->tags->map(function ($tag) use ($lang) {
                return new TagResponseDTO($tag, $lang);
            })->toArray();
        }
        if (in_array('ingredients', $relations) && $meal->relationLoaded('ingredients')) {
            $this->ingredients = $meal->ingredients->map(function ($ingredient) use ($lang) {
                return new IngredientResponseDTO($ingredient, $lang);
            })->toArray();
        }
        $this->with = $with;
    }

    /**
     * Determine the status of the meal for the response
     *
     * @param Meal $meal
     * @param $diff_time
     * @return string
     */
    public function determineStatus(Meal $meal, $diff_time): string
    {
        if (!$diff_time) return 'created';

        $createdAt = $meal->created_at ? $meal->created_at->getTimestamp() : null;
        $updatedAt = $meal->updated_at ? $meal->updated_at->getTimestamp() : null;
        $deletedAt = $meal->deleted_at ? $meal->deleted_at->getTimestamp() : null;

        $latest = max($createdAt, $updatedAt, $deletedAt ?? 0);

        if ($latest > $diff_time) {
            if ($deletedAt && $deletedAt > $diff_time) return 'deleted';
            if ($updatedAt > $createdAt) return 'modified';
        }
        return 'created';
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        $data = [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status
        ];

        $relations = explode(',', $this->with);

        if (in_array('category', $relations) && isset($this->category)) {
            $data['category'] = $this->category;
        }

        if (in_array('tags', $relations) && isset($this->tags)) {
            $data['tags'] = $this->tags;
        }

        if (in_array('ingredients', $relations) && isset($this->ingredients)) {
            $data['ingredients'] = $this->ingredients;
        }

        return $data;
    }
}
