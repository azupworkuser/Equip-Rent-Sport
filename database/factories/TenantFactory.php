<?php

namespace Database\Factories;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TenantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail() . '.' . uniqid(),
            'password' => $this->faker->password(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Tenant $tenant) {
            $tenant->domains()->create([
                'domain' => $this->faker->domainName() . uniqid(),
                'location_name' => 'test',
                'is_primary' => true,
                'location_name' => $this->faker->word,
            ]);
            $team = $tenant->teams()->create([
                'name' => 'admins'
            ]);

            $team->users()->attach(User::factory()->create(), [
                'id' => Str::orderedUuid()
            ]);
        });
    }
}
