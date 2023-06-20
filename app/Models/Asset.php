<?php

namespace App\Models;

use App\Models\States\AssetStates\ArchivedState;
use App\Models\States\AssetStates\AssetStates;
use App\Models\Traits\HasArchivedStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\ModelStates\HasStates;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Asset extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasUuids;
    use BelongsToTenant;
    use HasStates;
    use HasArchivedStatus;

    public const ARCHIVED_STATUS = ArchivedState::class;

    protected $fillable = [
        'name',
        'quantity',
        'capacity_per_quantity',
        'shared_between_products',
        'shared_between_bookings',
        'tenant_id',
        'domain_id',
        'created_by'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'capacity_per_quantity' => 'integer',
        'shared_between_products' => 'boolean',
        'shared_between_bookings' => 'boolean',
        'status' => AssetStates::class
    ];

    /**
     * @return BelongsToMany
     */
    public function categories(): BelongsToMany
    {
        return $this->morphToMany(Category::class, 'categorizable');
    }

    /**
     * @return int
     */
    public function getTotalCapacityAttribute(): int
    {
        return $this->quantity * $this->capacity_per_quantity;
    }
}
