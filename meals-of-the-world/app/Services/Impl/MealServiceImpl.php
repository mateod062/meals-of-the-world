<?php

namespace App\Services\Impl;

use App\Models\Meal;
use App\Repositories\MealRepository;
use App\Services\DTOs\MealDTO;
use App\Services\MealService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class MealServiceImpl implements MealService
{
    protected MealRepository $mealRepository;

    public function __construct(MealRepository $mealRepository)
    {
        $this->mealRepository = $mealRepository;
    }

    /**
     * @throws ValidationException
     */
    public function getAllMeals(array $params)
    {
        $validator = Validator::make($params, [
            'tags' => ['nullable', 'regex:/^(\d+,)*\d+$/'],
            'with' => ['nullable', 'regex:/^(ingredients|tags|category)(,(ingredients|tags|category))*$/'],
            'diff_time' => ['nullable', 'numeric'],
            'lang' => ['required', 'string', 'exists:languages,code'],
            'per_page' => ['nullable', 'integer', 'min:1'],
            'page' => ['nullable', 'integer', 'min:1']
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $query = Meal::query();

        $query->with([
            'translations' => function ($q) use ($params) {
                $q->where('language_code', $params['lang']);
            }
        ]);

        if (isset($params['category_id'])) {
            if ($params['category_id'] === 'NULL') {
                $query->whereNull('category_id');
            }
            elseif ($params['category_id'] === '!NULL') {
                $query->whereNotNull('category_id');
            }
            else {
                $query->where('category_id', $params['category_id']);
            }
        }

        if (!empty($params['tags'])) {
            $tags = is_array($params['tags']) ? $params['tags'] : explode(',', $params['tags']);
            if (count($tags) > 0) {
                $query->whereHas('tags', function ($q) use ($tags) {
                    $q->whereIn('id', $tags);
                }, '=', count($tags));
            }

        }

        if (!empty($params['diff_time'])) {
            $query->where(function ($q) use ($params) {
                $q->where('created_at', '>', Carbon::createFromTimestamp($params['diff_time']))
                    ->orWhere('updated_at', '>', Carbon::createFromTimestamp($params['diff_time']))
                    ->orWhere('deleted_at', '>', Carbon::createFromTimestamp($params['diff_time']));
            });
        }

        if (!empty($params['with'])) {
            $relations = array_intersect(explode(',', $params['with']), ['ingredients', 'tags', 'category']);
            foreach ($relations as $relation) {
                $query->with($relation . '.translations');
            }
        }

        $perPage = $params['per_page'] ?? 10;
        $page = $params['page'] ?? 1;

        $meals = $this->mealRepository->findAll($query, $perPage, $page);

        $result = $meals->getCollection()->transform(function ($meal) use ($params) {
            return new MealDTO($meal, $params['lang'], $params['with'] ?? '', $params['diff_time'] ?? null);
        });

        return [
            'meals' => $result,
            'pagination' => [
                'total' => $meals->total(),
                'count' => $meals->count(),
                'per_page' => $meals->perPage(),
                'current_page' => $meals->currentPage(),
                'total_pages' => $meals->lastPage()
            ]
        ];
    }

    public function getMealById($id)
    {
        return $this->mealRepository->findById($id);
    }

    public function createMeal(array $data)
    {
        return $this->mealRepository->save($data);
    }

    public function updateMeal($id, array $data)
    {
        $meal = $this->mealRepository->findById($id);
        return $this->mealRepository->update($meal, $data);
    }

    public function deleteMeal($id)
    {
        $meal = $this->mealRepository->findById($id);
        return $this->mealRepository->delete($meal);
    }
}
