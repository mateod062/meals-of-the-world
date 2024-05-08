<?php

namespace App\Services\Impl;

use App\Models\Category;
use App\Models\Meal;
use App\Repositories\MealRepository;
use App\Services\DTOs\MealDTO;
use App\Services\DTOs\MealResponseDTO;
use App\Services\MealService;
use InvalidArgumentException;
use RuntimeException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Service Implementation for managing the {@link Meal} model
 */
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
    public function getAllMeals(array $params): array
    {
        $validator = Validator::make($params, [
            'tags' => ['nullable', 'regex:/^(\d+,)*\d+$/'],
            'with' => ['nullable', 'regex:/^(ingredients|tags|category)(,(ingredients|tags|category))*$/'],
            'diff_time' => ['nullable', 'numeric'],
            'category' => ['nullable', 'string'],
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

        if (!empty($params['with'])) {
            $relations = array_intersect(explode(',', $params['with']), ['ingredients', 'tags', 'category']);
            foreach ($relations as $relation) {
                $query->with($relation . '.translations');
            }
        }

        if (isset($params['category'])) {
            if ($params['category'] === 'NULL') {
                $query->whereNull('category_id');
            }
            elseif ($params['category'] === '!NULL') {
                $query->whereNotNull('category_id');
            }
            else {
                $query->where('category_id', $params['category']);
            }
        }

        if (!empty($params['tags'])) {
            $tags = explode(',', $params['tags']);
            $count = count($tags);

            $query->where(function ($q) use ($tags, $count) {
                $q->whereHas('tags', function ($subQuery) use ($tags) {
                    $subQuery->whereIn('id', $tags);
                }, '=', $count);
            });

        }

        if (!empty($params['diff_time'])) {
            $query->where(function ($q) use ($params) {
                $q->where('created_at', '>', Carbon::createFromTimestamp($params['diff_time']))
                    ->orWhere('updated_at', '>', Carbon::createFromTimestamp($params['diff_time']))
                    ->orWhere('deleted_at', '>', Carbon::createFromTimestamp($params['diff_time']));
            });
        }

        $perPage = $params['per_page'] ?? 10;
        $page = $params['page'] ?? 1;

        $meals = $this->mealRepository->findAll($query, $perPage, $page);

        $result = $meals->getCollection()->transform(function ($meal) use ($params) {
            return new MealResponseDTO($meal, $params['lang'], $params['with'] ?? '', $params['diff_time'] ?? null);
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

    public function getMealById($id): MealDTO
    {
        return MealDTO::fromModel($this->mealRepository->findById($id));
    }

    /**
     * @throws ValidationException
     */
    public function createMeal(array $params): array
    {
        $data = [
            'category_id' => Category::where('slug', $params['category']['slug'])->firstOrFail()->id,
            'tags' => $params['tags'],
            'ingredients' => $params['ingredients']
        ];

        $validator = Validator::make($data, [
            'category_id' => ['nullable', 'exists:categories,id'],
            'tags' => ['required', 'array', 'min:1'],
            'tags.*' => ['exists:tags,slug'],
            'ingredients' => ['required', 'array', 'min:1'],
            'ingredients.*' => ['exists:ingredients,slug']
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $this->mealRepository->save($data);

        return $params;
    }

    /**
     * @throws ValidationException | RuntimeException | InvalidArgumentException
     */
    public function updateMeal($id, array $params): array
    {
        if (!isset($params['id']) || $params['id'] !== $id) {
            throw new InvalidArgumentException('ID mismatch');
        }

        $meal = $this->mealRepository->findById($id);

        $data = [
            'category_id' => Category::where('slug', $params['category']['slug'])->firstOrFail()->id,
            'tags' => $params['tags'],
            'ingredients' => $params['ingredients']
        ];

        $validator = Validator::make($data, [
            'category_id' => ['nullable', 'exists:categories,id'],
            'tags' => ['required', 'array', 'min:1'],
            'tags.*' => ['exists:tags,slug'],
            'ingredients' => ['required', 'array', 'min:1'],
            'ingredients.*' => ['exists:ingredients,slug']
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        if (!$this->mealRepository->update($meal, $data)) {
            throw new RuntimeException('Failed to update meal');
        }

        return $params;
    }

    /**
     * @throws RuntimeException
     */
    public function deleteMeal($id): void
    {
        $meal = $this->mealRepository->findById($id);

        if (!$this->mealRepository->delete($meal)) {
            throw new RuntimeException('Failed to delete meal');
        }
    }
}
