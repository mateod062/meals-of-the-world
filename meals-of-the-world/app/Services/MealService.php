<?php

namespace App\Services;

interface MealService
{
    public function getAllMeals(array $params);

    public function getMealById(int $id);

    public function createMeal(array $data);

    public function updateMeal(int $id, array $data);

    public function deleteMeal(int $id);
}
