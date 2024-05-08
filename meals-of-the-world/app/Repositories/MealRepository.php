<?php

namespace App\Repositories;

use App\Models\Meal;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Repository for managing the {@see Meal} model
 */
class MealRepository
{
    protected Meal $model;

    public function __construct(Meal $meal)
    {
        $this->model = $meal;
    }

    public function findAll(?Builder $query, int $perPage, int $page): LengthAwarePaginator
    {
        // If no query is presented, return all meals
        if (is_null($query)) {
            $query = $this->model->query();
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function findById(int $id)
    {
        return $this->model->where('id', $id)->first();
    }

    public function save(array $data): bool
    {
        $tagIds = $data['tags']->pluck('id')->toArray();
        $ingredientIds = $data['ingredients']->pluck('id')->toArray();

        $this->model->save($data['category_id']);

        $this->model->tags()->sync($tagIds);
        $this->model->ingredients()->sync($ingredientIds);

        return true;
    }

    public function update(Meal $meal, array $data): bool
    {
        return $meal->update($data);
    }

    public function delete(Meal $meal): ?bool
    {
        return $meal->delete();
    }

    public function restore(Meal $meal): bool
    {
        if ($meal->trashed()) {
            return $meal->restore();
        }
        return false;
    }
}
