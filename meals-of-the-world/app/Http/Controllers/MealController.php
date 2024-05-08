<?php

namespace App\Http\Controllers;

use App\Services\MealService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class MealController extends Controller
{
    protected MealService $mealService;

    public function __construct(MealService $mealService)
    {
        $this->mealService = $mealService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $meal = $this->mealService->createMeal($request->all());
        return response()->json($meal, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $meal = $this->mealService->getMealById($id);
        return response()->json($meal);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $meal = $this->mealService->updateMeal($id, $request->all());
        return response()->json($meal);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->mealService->deleteMeal($id);
        return response()->json(null, 204);
    }
}
