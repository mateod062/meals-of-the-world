<?php

namespace App\Http\Controllers;

use App\Services\MealService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use RuntimeException;

/**
 * @group Meals
 *
 * REST controller for managing the {@link Meal} model
 */
class MealController extends Controller
{
    protected MealService $mealService;

    public function __construct(MealService $mealService)
    {
        $this->mealService = $mealService;
    }

    /**
     * Display all meals
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $result = $this->mealService->getAllMeals($request->all());

            $response = [
                'meta' => [
                    'currentPage' => $result['pagination']['current_page'],
                    'totalItems' => $result['pagination']['total'],
                    'itemsPerPage' => $result['pagination']['per_page'],
                    'totalPages' => $result['pagination']['total_pages'],
                ],
                'data' => $result['meals'],
                'links' => [
                    'prev' => $result['pagination']['current_page'] > 1 ? $request->fullUrlWithQuery(['page' => $result['pagination']['current_page'] - 1]) : null,
                    'next' => $result['pagination']['current_page'] < $result['pagination']['total_pages'] ? $request->fullUrlWithQuery(['page' => $result['pagination']['current_page'] + 1]) : null,
                    'self' => $request->fullUrl()
                ]
            ];
            return response()->json($response);
        } catch (ValidationException $ex) {
            return response()->json([
                'errors' => $ex->validator->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Internal server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a new meal
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $meal = $this->mealService->createMeal($request->all());
            return response()->json($meal, 201);
        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->validator->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Internal server error',
                'error' => $e->getMessage()
            ], 500);
        }

    }

    /**
     * Display the specified meal
     */
    public function show(string $id): JsonResponse
    {
        try {
            $meal = $this->mealService->getMealById($id);
            return response()->json($meal);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Internal server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified meal
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $meal = $this->mealService->updateMeal($id, $request->all());
            return response()->json($meal);
        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->validator->errors()
            ], 422);
        } catch (RuntimeException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Internal server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified meal
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $this->mealService->deleteMeal($id);
            return response()->json(null, 204);
        } catch (RuntimeException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Internal server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
