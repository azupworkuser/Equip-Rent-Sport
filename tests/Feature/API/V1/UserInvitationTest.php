<?php

namespace Tests\Feature\API\V1;

use App\Models\Role;
use App\Models\States\UserInvitation\Draft;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserInvitationTest extends TestCase
{
    use DatabaseMigrations;
    use WithFaker;

    public function test_if_invitation_can_be_created()
    {
        $role = Role::all()->random()->getKey();

        $payload = [
            'email' => $this->faker->email(),
            'status' => Draft::class,
            'domains' => [
                [
                    'domain_id' => $this->domain->getKey(),
                    'role_id' => $role
                ]
            ]
        ];
        $final_payload = $payload;
        $final_payload['first_name'] = $this->faker->firstName;
        $final_payload['last_name'] = $this->faker->lastName;
        $final_payload['phone_number'] = $this->faker->phoneNumber;

        $this->withoutExceptionHandling();
        $this
            ->actingAs($this->user, 'api')
            ->postJson('/api/v1/user-invitation', $final_payload)
            ->assertStatus(201)
            ->assertSuccessful();
        unset($payload['domains']);
        $payload['status'] = 'sent';
        $this->assertDatabaseHas('user_invitations', $payload);
    }

    public function test_if_all_required_fields_are_empty()
    {
        $payload = [];
        $this
            ->actingAs($this->user, 'api')
            ->postJson('/api/v1/user-invitation', $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'email',
                'status',
                'domains',
                'first_name',
                'last_name',
                'phone_number'
            ]);
    }

    public function test_if_invalid_email()
    {
        $role = Role::all()->random();
        $payload = [
            'email' => $this->faker->name(),
            'status' => Draft::class,
            'domains' => [
                [
                    'domain_id' => $this->domain->getKey(),
                    'role_id' => $role->getKey()
                ]
            ],
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'phone_number' => $this->faker->phoneNumber,
        ];

        $this
            ->actingAs($this->user, 'api')
            ->postJson('/api/v1/user-invitation', $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'email'
            ]);
    }
}
