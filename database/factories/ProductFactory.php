<?php

namespace Database\Factories;

use App\Models\AvailabilitySession;
use App\Models\Product;
use App\Models\ProductOption;
use App\Models\ProductAvailability;
use App\Models\ProductOptionAvailabilityEvent;
use App\Models\ProductType;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
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
            'name' => $this->faker->name,
            'description' => $this->faker->text,
            'visibility' => Product::VISIBILITY_TYPE['Everyone'],
            'advertised_price' => $this->faker->numberBetween(100, 10000),
            'terms_and_conditions' => $this->faker->text,
            'product_type_id' => fn() => ProductType::factory()->create(),
            'tenant_id' => $tenant->getKey(),
            'domain_id' => $tenant->primary_domain->getKey(),
            'created_by' => $tenant->teams->first()->users->first()->getKey()
        ];
    }
}
