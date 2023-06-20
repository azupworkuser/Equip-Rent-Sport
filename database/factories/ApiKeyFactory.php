<?php

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApiKeyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'key' => \Str::random(32),
            'name' => $this->faker->name,
            'tenant_id' => fn() => Tenant::factory()->create()->getKey(),
            'domain_id' => fn() => 1,
            'data' => [
                'whitelist_ips' => $this->faker->ipv4
            ]
        ];
    }
}
