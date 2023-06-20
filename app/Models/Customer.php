<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Customer extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasUuids;
    use BelongsToTenant;

    protected $fillable = [
        'first_name',
        'last_name',
        'tenant_id',
        'domain_id',
        'created_by',
        'email',
        'phone',
        'phone_code',
        'address1',
        'address2',
        'city',
        'state',
        'country',
        'zip_code'
    ];
}
