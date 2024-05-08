<?php

namespace App\Services;

use App\Services\DTOs\MealDTO;

/**
 * Service Interface for managing the {@link Meal} model
 */
interface MealService
{
    /**
     * Get all meals
     *
     * @param array $params ['lang', 'tags', 'page', 'per_page', 'with', 'diff_time']
     * @return array
     */
    public function getAllMeals(array $params): array;

    /**
     * Get meal by id
     *
     * @param int $id
     * @return MealDTO
     */
    public function getMealById(int $id): MealDTO;

    /**
     * Create a new meal
     *
     * @param array $params
     * @return array
     */
    public function createMeal(array $params): array;

    /**
     * Update meal by id
     *
     * @param int $id
     * @param array $params
     * @return array
     */
    public function updateMeal(int $id, array $params): array;

    /**
     * Delete meal by id
     *
     * @param int $id
     * @return void
     */
    public function deleteMeal(int $id): void;
}
