<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductOption;
use App\Models\ProductAvailability;
use App\Models\ProductOptionAvailabilityEvent;
use App\Models\States\ProductAvailabilityEvent\Active;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductOptionAvailabilityEvent>
 */
class ProductOptionAvailabilityEventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $tenant = TenantFactory::new()->create();
        $product = $product = Product::factory()->create([
            'tenant_id' => $tenant->getKey(),
            'domain_id' => $tenant->primary_domain->getKey(),
            'created_by' => $tenant->teams->first()->users->first()->getKey(),
        ]);
        return [
            'name' => $this->faker->word,
            'local_start_date' => now()->format('Y-m-d'),
            'local_end_date' => now()->addMonth()->format('Y-m-d'),
            'status' => Active::class,
            'product_option_availability_id' => ProductAvailability::factory()->create([
                'product_option_id' => $product->options->first()->getKey(),
                'tenant_id' => $tenant->getKey(),
                'domain_id' => $tenant->primary_domain->getKey(),
                'created_by' => $tenant->teams->first()->users->first()->getKey(),
            ])->getKey(),
            'product_option_id' => ProductOption::factory()->create([
                'product_id' => $product->getKey(),
                'tenant_id' => $tenant->getKey(),
                'domain_id' => $tenant->primary_domain->getKey(),
                'created_by' => $tenant->teams->first()->users->first()->getKey(),
            ]),
            'tenant_id' => $tenant->getKey(),
            'domain_id' => $tenant->primary_domain->getKey(),
            'created_by' => $tenant->teams->first()->users->first()->getKey(),
        ];
    }
}
