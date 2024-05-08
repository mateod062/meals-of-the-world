<?php

namespace App\Repositories;

use App\Models\Meal;
use Illuminate\Database\Eloquent\Builder;
class MealRepository
{
    protected Meal $model;

    public function __construct(Meal $meal)
    {
        $this->model = $meal;
    }

    public function findAll(?Builder $query, int $perPage, int $page)
    {
        if (is_null($query)) {
            $query = $this->model->query();
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function findById(int $id)
    {
        return $this->model->find($id);
    }

    public function save(array $data)
    {
        return $this->model->save($data);
    }

    public function update(Meal $meal, array $data)
    {
        return $meal->update($data);
    }

    public function delete(Meal $meal)
    {
        return $meal->delete();
    }
}
