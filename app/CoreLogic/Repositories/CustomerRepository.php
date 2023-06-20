<?php

namespace App\CoreLogic\Repositories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

class CustomerRepository extends BaseRepository
{
    public string $modelName = Customer::class;
}
