<?php

namespace App\Models;

use App\CoreLogic\States\Product\ProductState;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\ModelStates\HasStates;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use App\Models\Traits\HasOffers;

class Product extends Model
{
    use HasFactory;
    use HasStates;
    use BelongsToTenant;
    use HasUuids;
    use HasOffers;

    protected $fillable = [
        'name',
        'description',
        'visibility',
        'code',
        'advertised_price',
        'terms_and_conditions',
        'product_type_id',
        'status',
        'tenant_id',
        'domain_id',
        'created_by'
    ];

    protected $casts = [
        'status' => ProductState::class
    ];

    public const VISIBILITY_TYPE = [
        'Everyone' => 'Everyone',
        'staff' => 'staff',
        'extra' => 'extra',
    ];

    /**
     * @return HasMany
     */
    public function media(): HasMany
    {
        return $this->hasMany(ProductMedia::class);
    }

    /**
     * @return HasMany
     */
    public function locations(): HasMany
    {
        return $this->hasMany(ProductLocation::class);
    }

    /**
     * @return BelongsTo
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(ProductType::class, 'product_type_id');
    }

    /**
     * @return HasOne
     */
    public function inventory(): HasOne
    {
        return $this->hasOne(ProductInventory::class);
    }

    /**
     * @return HasMany
     */
    public function availabilities(): HasMany
    {
        return $this->hasMany(ProductAvailability::class);
    }
}
