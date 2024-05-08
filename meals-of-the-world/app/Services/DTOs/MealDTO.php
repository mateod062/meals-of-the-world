<?php

namespace App\Services\DTOs;

use App\Models\Meal;
use Illuminate\Support\Carbon;

class MealDTO
{
    public int $id;
    public string $title;
    public string $description;
    public string $status;
    public CategoryDTO $category;
    public array $tags;
    public array $ingredients;

    public function __construct(Meal $meal, string $lang, $with = '', $diff_time = null)
    {
        $this->id = $meal->id;
        $this->title = $meal->translations->where('language_code', $lang)->first()->title ?? '';
        $this->description = $meal->translations->where('language_code', $lang)->first()->description ?? '';
        $this->status = $this->determineStatus($meal, $diff_time);

        $relations = explode(',', $with);
        if (in_array('category', $relations) && $meal->relationLoaded('category')) {
            $this->category = new CategoryDTO($meal->category, $lang);
        }
        if (in_array('tags', $relations) && $meal->relationLoaded('tags')) {
            $this->tags = $meal->tags->map(function ($tag) use ($lang) {
                return new TagDTO($tag, $lang);
            })->toArray();
        }
        if (in_array('ingredients', $relations) && $meal->relationLoaded('ingredients')) {
            $this->ingredients = $meal->ingredients->map(function ($ingredient) use ($lang) {
                return new IngredientDTO($ingredient, $lang);
            })->toArray();
        }
    }

    public function determineStatus(Meal $meal, $diff_time)
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
}
