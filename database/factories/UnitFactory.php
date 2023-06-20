<?php

namespace Database\Factories;

use App\Models\Tenant;
use App\Models\Unit;
use App\Models\UnitType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<Unit>
 */
class UnitFactory extends Factory
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
            'unit_type_id' => UnitType::factory()->create()->getKey(),
            'internal_name' => $this->faker->name,
            'description' => $this->faker->text,
            'seats_used' => $this->faker->numberBetween(1, 10),
            'id_required' => $this->faker->boolean,
            'tenant_id' => $tenant->getKey(),
            'domain_id' => $tenant->primary_domain->getKey(),
            'created_by' => $tenant->teams->first()->users->first()->getKey(),
        ];
    }
}
