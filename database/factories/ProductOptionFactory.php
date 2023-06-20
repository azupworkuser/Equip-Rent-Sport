<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductOption>
 */
class ProductOptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $tenant = Tenant::factory()->create();
        return [
            'product_id' => fn() => Product::factory()->create(['tenant_id' => $tenant->getKey()]),
            'tenant_id' => $tenant->getKey(),
            'domain_id' => $tenant->primary_domain->getKey(),
            'created_by' => $tenant->teams->first()->users->first()->getKey(),
        ];
    }
}
