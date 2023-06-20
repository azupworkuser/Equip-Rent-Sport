<?php

namespace Tests\Feature\API\V1;

use App\Models\Role;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use DatabaseMigrations;
    use WithFaker;

    public function test_if_role_can_be_created()
    {
        $create_payload = $payload = [
            'name' => $this->faker->word(),
            'guard_name' => \App\Models\Permission::GUARD,
            'tenant_id' => $this->tenant->getKey()
        ];
        $create_payload['permissions'] = [Permission::all()->random()->name];

        $this->withoutExceptionHandling();
        $this
            ->actingAs($this->user, 'api')
            ->postJson('/api/v1/role', $create_payload)
            ->assertStatus(201)
            ->assertSuccessful();
        $this->assertDatabaseHas('roles', $payload);
    }

    public function test_if_all_required_fields_are_empty()
    {
        $payload = [];
        $this
            ->actingAs($this->user, 'api')
            ->postJson('/api/v1/role', $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'name',
                'permissions'
            ]);
    }

    public function test_if_invalid_permission()
    {
        $create_payload = [
            'name' => $this->faker->word(),
            'guard_name' => \App\Models\Permission::GUARD,
            'tenant_id' => $this->tenant->getKey()
        ];
        $create_payload['permissions'] = [$this->faker->word()];

        $this
            ->actingAs($this->user, 'api')
            ->postJson('/api/v1/role', $create_payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'permissions'
            ]);
    }

    public function test_if_role_can_be_updated()
    {
        $payload = [
            'name' => $this->faker->word(),
            'guard_name' => \App\Models\Permission::GUARD,
            'tenant_id' => $this->tenant->getKey()
        ];
        $role = Role::create($payload);

        $updated_role = [
            'name' => $this->faker->word(),
            'guard_name' => \App\Models\Permission::GUARD,
            'tenant_id' => $this->tenant->getKey()
        ];
        $updated_role['permissions'] = [Permission::all()->random()->name];

        $this
            ->actingAs($this->user, 'api')
            ->putJson('/api/v1/role/' . $role->getKey(), $updated_role)->assertStatus(200);

        unset($updated_role['permissions']);
        $this->assertDatabaseHas('roles', array_merge($updated_role, [
            'tenant_id' => $this->tenant->getKey()
        ]));
    }

    public function test_if_role_can_be_deleted()
    {
        $payload = [
            'name' => $this->faker->word(),
            'guard_name' => \App\Models\Permission::GUARD,
            'tenant_id' => $this->tenant->getKey()
        ];
        $role = Role::create($payload);

        $this
            ->actingAs($this->user, 'api')
            ->deleteJson('/api/v1/role/' . $role->getKey(), [])
            ->assertStatus(200)
            ->assertJsonStructure([
                'message'
            ]);
        $this->assertDatabaseMissing('roles', $payload);
    }
}
