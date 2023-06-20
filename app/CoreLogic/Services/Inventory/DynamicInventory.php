<?php

namespace App\CoreLogic\Services\Inventory;

use App\Models\Product;
use App\Models\ProductAvailabilitySlot;
use App\Models\ProductInventory;
use App\Models\States\ProductAvailabilitySlot\Available;
use App\Models\States\ProductAvailabilitySlot\Booked;
use App\Models\States\ProductAvailabilitySlot\Hold;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class DynamicInventory extends BaseInventory implements InventoryContract
{
    /**
     * @param Product $product
     * @param array $data
     * @return ProductInventory
     */
    public function create(Product $product, array $data): ProductInventory
    {
        $productInventory = $this->repository->create([
            'product_id' => $product->getKey(),
            'inventory_type' => $data['inventory_type'],
        ]);

        $productInventory->assets()->sync($data['assets'] ?? []);

        return $productInventory;
    }

    /**
     * @param ProductInventory $inventory
     * @param Carbon $date
     * @return mixed
     */
    public function checkAvailability(ProductInventory $inventory, Carbon $date)
    {
        $assets = $inventory->assets;

        $inventories = ProductInventory::whereHas('assets', function (Builder $query) use ($assets) {
            $query->whereIn('id', $assets->pluck('id')->toArray());
        })->get();

        return ProductAvailabilitySlot::whereHas('availability', function (Builder $query) use ($inventories) {
            $query->whereIn('product_id', $inventories->pluck('product_id')->toArray());
        })->where('start_at', 'like', $date->format('Y-m-d ') . '%')
            ->get()
            ->groupBy('start_at')
            ->map(function ($slots, $key) {
                $byStatus = $slots->groupBy('status');
                return [
                    'available' => count($byStatus[Available::class] ?? []),
                    'booked' => count($byStatus[Booked::class] ?? []),
                    'hold' => count($byStatus[Hold::class] ?? []),
                    'slot' => $key
                ];
            });
    }
}
