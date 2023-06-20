<?php

namespace Tests\Feature\API\V1;

use App\Models\Deductible;
use App\CoreLogic\Enum\Deductible\DeductibleCategoryEnum;
use App\CoreLogic\Enum\Deductible\DeductibleTypeEnum;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DeductibleTest extends TestCase
{
    use DatabaseMigrations;
    use WithFaker;

    public function test_if_deductible_can_be_created()
    {
        $payload = [
            'name' => $this->faker->word(),
            'category' => $this->faker->randomElement(DeductibleCategoryEnum::cases()),
            'type' => $this->faker->randomElement(DeductibleTypeEnum::cases()),
            'value' => $this->faker->randomFloat('2', 0, 100),
            'is_price_inclusive' => $this->faker->boolean,
            'is_compounded' => $this->faker->boolean
        ];

        $this->withoutExceptionHandling();
        $this
            ->actingAs($this->user, 'api')
            ->postJson('/api/v1/deductible', $payload)
            ->assertStatus(201)
            ->assertSuccessful();
        $this->assertDatabaseHas('deductibles', $payload);
    }

    public function test_if_all_required_fields_are_empty()
    {
        $payload = [];
        $this
            ->actingAs($this->user, 'api')
            ->postJson('/api/v1/deductible', $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'name',
                'category',
                'type',
                'value'
            ]);
    }

    public function test_if_invalid_category()
    {
        $payload = [
            'name' => $this->faker->word(),
            'category' => 3,
            'type' => $this->faker->randomElement(DeductibleTypeEnum::cases()),
            'value' => $this->faker->randomFloat('2', 0, 100),
            'is_price_inclusive' => $this->faker->boolean,
            'is_compounded' => $this->faker->boolean
        ];
        $this
            ->actingAs($this->user, 'api')
            ->postJson('/api/v1/deductible', $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'category'
            ]);
    }


    public function test_if_list_of_all_deductibles_are_returned()
    {
        $deductibles = Deductible::factory()->count(5)->create([
            'tenant_id' => $this->tenant->getKey(),
            'created_by' => $this->user->getKey(),
            'domain_id' => $this->tenant->primary_domain->getKey()
        ]);
        $this
            ->actingAs($this->user, 'api')
            ->getJson('/api/v1/deductible')
            ->assertSuccessful()
            ->assertJsonStructure([
                'data'
            ])->assertJsonCount(5, 'data');
    }


    public function test_if_deductible_can_be_updated()
    {
        $deductible = Deductible::factory()->create([
            'tenant_id' => $this->tenant->getKey(),
            'created_by' => $this->user->getKey(),
            'domain_id' => $this->tenant->primary_domain->getKey()
        ]);

        $updated_deductible = [
            'name' => $this->faker->word() . uniqid(),
            'category' => $this->faker->randomElement(DeductibleCategoryEnum::cases()),
            'type' => $this->faker->randomElement(DeductibleTypeEnum::cases()),
            'value' => $this->faker->randomFloat('2', 0, 100),
            'is_price_inclusive' => $this->faker->boolean,
            'is_compounded' => $this->faker->boolean,
        ];

        $this
            ->actingAs($this->user, 'api')
            ->putJson('/api/v1/deductible/' . $deductible->getKey(), $updated_deductible)->assertStatus(200);

        $this->assertDatabaseHas('deductibles', array_merge($updated_deductible, [
            'tenant_id' => $this->tenant->getKey(),
            'domain_id' => $this->tenant->primary_domain->getKey()
        ]));
    }

    public function test_if_deductible_can_be_deleted()
    {
        $deductible = Deductible::factory()->create([
            'tenant_id' => $this->tenant->getKey(),
            'created_by' => $this->user->getKey(),
            'domain_id' => $this->tenant->primary_domain->getKey()
        ]);

        $this
            ->actingAs($this->user, 'api')
            ->deleteJson('/api/v1/deductible/' . $deductible->getKey(), [])
            ->assertStatus(200)
            ->assertJsonStructure([
                'message'
            ]);
        $this->assertSoftDeleted($deductible);
    }
}
