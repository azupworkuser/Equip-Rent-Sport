<?php

namespace Database\Factories;

use App\Models\Domain;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'description' => $this->faker->text,
            'type' => $this->faker->randomElement(['product', 'asset']),
            'tenant_id' => fn() => Tenant::factory()->create(),
            'domain_id' => 1,
            'created_by' => fn() => User::factory()->create(),
        ];
    }
}
