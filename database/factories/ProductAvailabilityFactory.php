<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductAvailability;
use App\Models\Tenant;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductAvailability>
 */
class ProductAvailabilityFactory extends Factory
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
            'name' => $this->faker->words(3, true),
            'duration' => 1, // hour
            'duration_type' => 'hour',
            'start_date' => Carbon::now()->addDays(1)->format('Y-m-d H:i:s'),
            'end_date' => Carbon::now()->addDays(2)->format('Y-m-d H:i:s'),
            'start_time' => Carbon::now()->format('G:i'),
            'end_time' => Carbon::now()->addHours(8)->format('G:i'),
            'increment' => 1, // hours
            'increment_type' => 'hour',
            'available_days' => [1, 2, 3, 4, 5],
            'product_id' => fn() => Product::factory()->create(),
            'tenant_id' => $tenant->getKey(),
            'domain_id' => $tenant->primary_domain->getKey(),
            'created_by' => $tenant->teams->first()->users->first()
        ];
    }
}
