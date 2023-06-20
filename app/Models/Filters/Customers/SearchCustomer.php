<?php

namespace App\Models\Filters\Customers;

use Illuminate\Database\Eloquent\Builder;

class SearchCustomer
{
    public function __invoke(Builder $query, $value)
    {
        $query
              ->where(function ($clause) use ($value) {
                  return $clause->where('first_name', 'like', '%' . $value . '%')
                        ->orWhere('last_name', 'like', '%' . $value . '%')
                        ->orWhere('email', 'like', '%' . $value . '%')
                        ->orWhere('phone', 'like', '%' . $value . '%');
              });
    }
}
