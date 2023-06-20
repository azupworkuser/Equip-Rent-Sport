<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductOption;
use App\Models\ProductOptionAvailabilityEvent;
use App\Models\Tenant;
use App\Models\Unit;
use App\CoreLogic\Enum\ProductPricingTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductPricing>
 */
class ProductPricingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $tenant = Tenant::factory()->create();
        $common = [
            'tenant_id' => $tenant->getKey(),
            'domain_id' => $tenant->primary_domain->getKey(),
        ];
        $product = Product::factory()->create($common);

        return [
            'product_id' => $product->getKey(),
            'unit_id' => fn() => Unit::factory()->create($common),
            'min_quantity' => $this->faker->numberBetween(1, 10),
            'max_quantity' => $this->faker->numberBetween(11, 20),
            'price' => $this->faker->randomFloat(2, 1, 100),
            'price_type' => $this->faker->randomElement(['total', 'per_unit']),
            'pricing_structure_type' => $this->faker->randomElement(
                array_keys((new \ReflectionClass(ProductPricingTypeEnum::class))->getConstants())
            )
        ];
    }
}
