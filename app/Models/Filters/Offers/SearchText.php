<?php

namespace App\Models\Filters\Offers;

use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class SearchText implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {
        $query->where('code', 'like', '%' . $value . '%')
            ->orWhere('title', 'like', '%' . $value . '%');
    }
}
