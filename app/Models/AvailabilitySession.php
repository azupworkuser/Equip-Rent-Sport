<?php

namespace App\Models;

use App\Models\States\ProductAvailabilitySlot\ProductAvailabilitySlotState;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class AvailabilitySession extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasUuids;
    use BelongsToTenant;

    public const DURATION_TYPES = [
        'minutes' => 'Minutes',
        'hours' => 'Hours',
        'days' => 'Days',
    ];

    protected $casts = [
        'status' => ProductAvailabilitySlotState::class
    ];

    protected $fillable = [
        'local_start_time',
        'local_end_time',
        'local_start_date',
        'local_end_date',
        'duration_unit',
        'duration_type',
        'status',
        'product_availability_id',
        'product_option_id',
        'created_by',
    ];

    /**
     * @return BelongsTo
     */
    public function productOption(): BelongsTo
    {
        return $this->belongsTo(ProductOption::class);
    }

    /**
     * @return BelongsTo
     */
    public function productOptionAvailabilityEvent(): BelongsTo
    {
        return $this->belongsTo(ProductOptionAvailabilityEvent::class);
    }
}
