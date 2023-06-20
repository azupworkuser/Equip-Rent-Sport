<?php

namespace App\CoreLogic\Services;

use App\Models\Offer;
use App\CoreLogic\Repositories\Repository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Spatie\QueryBuilder\QueryBuilder;

abstract class Service
{
    protected Repository $repository;
    public array $viewData = [];

    public function __construct()
    {
        if (property_exists($this, 'repositoryName')) {
            $this->repository = resolve($this->repositoryName);
        }
    }

    /**
     * @param $property
     * @return mixed|null
     */
    public function __get($property)
    {
        return $this->viewData[$property] ?? null;
    }

    /**
     * @param string $property
     * @param $data
     * @return void
     */
    public function __set(string $property, $data): void
    {
        $this->viewData[$property] = $data;
    }

    /**
     * @param $property
     * @return bool
     */
    public function __isset($property): bool
    {
        return isset($this->viewData[$property]);
    }

    /**
     * @param bool $paginate
     * @param array $allowedFilters
     * @param array $allowedSorts
     * @param array $load
     * @return Collection|LengthAwarePaginator
     */
    public function all(
        bool $paginate = false,
        array $allowedFilters = [],
        array $allowedSorts = [],
        array $load = []
    ): Collection|LengthAwarePaginator {
        $query = QueryBuilder::for($this->repository->modelName);

        if (count($allowedFilters) > 0) {
            $query = $query->allowedFilters($allowedFilters);
        }

        if (count($allowedSorts) > 0) {
            $query = $query->allowedSorts($allowedSorts);
        }

        if (count($load) > 0) {
            $query = $query->allowedIncludes($load);
        }

        return $paginate ? $query->paginate() : $query->get();
    }

    /**
     * @param array|string $ids
     * @param array $load
     * @param array $filters
     * @return mixed
     */
    public function find(array|string $ids, array $load = [], array $filters = []): mixed
    {
        $query = QueryBuilder::for($this->repository->model);

        if (count($load) > 0) {
            $query = $query->allowedIncludes($load);
        }

        if (count($filters) > 0) {
            $query = $query->allowedFilters($filters);
        }

        return $query->findOrFail($ids);
    }
}
