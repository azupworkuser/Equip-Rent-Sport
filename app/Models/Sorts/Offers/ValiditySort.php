<?php

namespace App\Models\Sorts\Offers;

use Spatie\QueryBuilder\Sorts\Sort;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ValiditySort implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property)
    {
        $direction = $descending ? 'desc' : 'asc';
        $query->orderBy(
            DB::raw('case when expired_at is null then ' . (int) ($direction == 'asc') . ' else ' . (int) ($direction == 'desc') . ' end, expired_at'),
            $direction
        );
    }
}
