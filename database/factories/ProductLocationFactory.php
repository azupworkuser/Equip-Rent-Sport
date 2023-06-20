<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductLocation;
use App\Models\ProductOption;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductLocation>
 */
class ProductLocationFactory extends Factory
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
            'product_id' => fn() => Product::factory()->create(),
            'address_1' => $this->faker->streetAddress,
            'address_2' => $this->faker->secondaryAddress,
            'city' => $this->faker->city,
            'state' => $this->faker->state,
            'country' => $this->faker->country,
            'postal_code' => $this->faker->postcode,
            'map_link' => $this->faker->url,
            'lat' => $this->faker->latitude,
            'long' => $this->faker->longitude,
            'address_type' => ProductLocation::ADDRESS_TYPE['redeem_point'],
            'tenant_id' => $tenant->getKey(),
            'domain_id' => $tenant->domain_id,
            'created_by' => fn() => User::factory()->create(),
        ];
    }
}
