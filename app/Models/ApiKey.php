<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class ApiKey extends Model
{
    use HasFactory;
    use HasRoles;
    use SoftDeletes;
    use BelongsToTenant;
    use HasUuids;

    protected $guard_name = Permission::GUARD;

    protected $casts = [
        'data' => 'array',
    ];

    protected $fillable = [
        'name',
        'key',
        'data',
        'tenant_id',
        'domain_id'
    ];

    /**
     * @return string[]
     */
    public static function getCustomColumns(): array
    {
        return [
            'whitelist_ips'
        ];
    }
}
