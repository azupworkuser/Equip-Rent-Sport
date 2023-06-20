<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductAvailability;
use App\Models\AvailabilitySession;
use App\Models\ProductOptionAvailabilityEvent;
use App\Models\States\ProductAvailabilitySlot\Available;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AvailabilitySession>
 */
class AvailabilitySessionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $tenant = Tenant::factory()->create();
        $product = Product::factory()->create([
            'tenant_id' => $tenant->getKey(),
            'domain_id' => $tenant->primary_domain->getKey(),
            'created_by' => $tenant->teams->first()->users->first()
        ]);
        return [
            'local_start_date' => now()->format('Y-m-d'),
            'local_end_date' => now()->addDays(7)->format('Y-m-d'),
            'local_start_time' => '09:00:00',
            'local_end_time' => '17:00:00',
            'duration_unit' => $this->faker->randomDigit(),
            'duration_type' => $this->faker->randomElement(AvailabilitySession::DURATION_TYPES),
            'status' => Available::class,
            'product_option_availability_event_id' => fn() => ProductOptionAvailabilityEvent::factory()->create([
                'product_option_id' => $product->options->first()->getKey(),
                'tenant_id' => $tenant->getKey(),
                'domain_id' => $tenant->primary_domain->getKey(),
                'created_by' => $tenant->teams->first()->users->first()
            ])->getKey(),
            'tenant_id' => $tenant->getKey(),
            'domain_id' => $tenant->primary_domain->getKey(),
            'created_by' => $tenant->teams->first()->users->first()
        ];
    }
}
