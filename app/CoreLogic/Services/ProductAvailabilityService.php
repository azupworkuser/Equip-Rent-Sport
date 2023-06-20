<?php

namespace App\CoreLogic\Services;

use App\Jobs\ProduceProduceAvailabilitySlotsJob;
use App\Models\Product;
use App\Models\ProductAvailability;
use App\Models\ProductAvailabilitySlot;
use App\Models\ProductInventory;
use App\Models\States\ProductAvailabilitySlot\Available;
use App\Models\States\ProductAvailabilitySlot\Hold;
use App\Models\Unit;
use App\Models\UnitType;
use App\CoreLogic\Repositories\ProductAvailabilityRepository;
use App\CoreLogic\Services\Inventory\Inventory;
use Carbon\Carbon;
use http\Exception\RuntimeException;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

class ProductAvailabilityService extends Service
{
    public string $repositoryName = ProductAvailabilityRepository::class;

    /**
     * @param Product $product
     * @param array $data
     * @return ProductAvailability
     */
    public function update(Product $product, array $data): ProductAvailability
    {
        $availability = $this->repository->updateOrCreate([
            'product_id' => $product->first()->getKey(),
        ], $data);

        ProduceProduceAvailabilitySlotsJob::dispatch($availability);

        return $availability;
    }

    /**
     * @param Product $product
     * @param Carbon $date
     * @return mixed
     */
    public function getAvailability(Product $product, Carbon $date)
    {
        return $this->repository->getByDate($product, $date);
    }

    /**
     * @param Product $product
     * @param Carbon $date
     * @return mixed
     */
    public function checkAvailability(Product $product, Carbon $date)
    {
        $inventory = Inventory::factory($product->inventory->inventory_type);

        return $inventory->checkAvailability($product->inventory, $date);
    }

    /**
     * @param Product $product
     * @param Carbon $slotDateTime
     * @param int $quantity
     * @return mixed
     */
    public function holdSlot(Product $product, Carbon $slotDateTime, int $quantity = 1)
    {
        $slots = $this->checkAvailability($product, $slotDateTime)->toArray();

        $slotAvailability = $slots[$slotDateTime->format('Y-m-d H:i:00')];

        if ($slotAvailability['available'] < $quantity) {
            throw new RuntimeException("Not enough capacity on slot [$slotDateTime] available");
        }

        return ProductAvailabilitySlot::where('start_at', $slotDateTime->format('Y-m-d H:i:00'))
            ->where('product_availability_id', $product->availabilities->first()->getKey())
            ->where('status', Available::class)
            ->take($quantity)
            ->get()
            ->each(function (ProductAvailabilitySlot $slot) {
                $slot->status->hold();
                $slot->save();
            });
    }
}
