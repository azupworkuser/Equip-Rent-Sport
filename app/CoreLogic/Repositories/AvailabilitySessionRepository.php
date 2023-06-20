<?php

namespace App\CoreLogic\Repositories;

use App\Models\ProductOption;
use App\Models\AvailabilitySession;
use App\Models\States\ProductAvailabilitySlot\Available;
use App\CoreLogic\States\Product\Active;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AvailabilitySessionRepository extends BaseRepository
{
    public string $modelName = AvailabilitySession::class;

    /**
     * @param ProductOption $productOption
     * @param string $date
     * @return Collection
     */
    public function getByProductOptionAndDate(ProductOption $productOption, string $date): Collection
    {
        return $this
                ->model
                ->where('product_option_id', $productOption->getKey())
                ->whereDate('local_start_date', $date)
                ->get();
    }
}
