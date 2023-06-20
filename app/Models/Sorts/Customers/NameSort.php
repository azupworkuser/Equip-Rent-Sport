<?php

namespace App\Models\Sorts\Customers;

use Spatie\QueryBuilder\Sorts\Sort;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class NameSort implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property)
    {
        $direction = $descending ? 'desc' : 'asc';
        $query->orderBy('first_name', $direction)->orderBy('last_name', $direction);
    }
}
