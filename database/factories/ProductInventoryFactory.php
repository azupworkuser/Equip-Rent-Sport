<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductInventory;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductInventory>
 */
class ProductInventoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $tenant = Tenant::find($this->states->get('tenant_id')) ?? Tenant::factory()->create();

        return [
            'inventory_type' => ProductInventory::INVENTORY_TYPE_DYNAMIC,
            'quantity' => null,
            'product_id' => fn() => Product::factory()->create([
                'tenant_id' => $tenant->getKey(),
                'domain_id' => $tenant->primary_domain->getKey(),
                'created_by' => $tenant->teams->first()->users->first()->getKey(),
            ]),
            'created_by' => $tenant->teams->first()->users->first()->getKey(),
            'tenant_id' => $tenant->getKey(),
            'domain_id' => $tenant->primary_domain->getKey(),
        ];
    }
}
