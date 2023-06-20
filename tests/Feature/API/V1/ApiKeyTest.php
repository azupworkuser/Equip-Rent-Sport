<?php

namespace Tests\Feature\API\V1;

use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;
use App\Models\ApiKey;
use Spatie\Permission\Models\Permission;

class ApiKeyTest extends TestCase
{
    use DatabaseMigrations;
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $this->tenant = $this->createTenant();
        $this->user = $this->tenant->teams->first()->users->first();
    }

    public function test_if_api_keys_can_be_created()
    {
        $payload = [
            'name' => $this->faker->name,
            'permissions' => [Permission::all()->random()->getKey()],
            'whitelist_ips' => '192.190.89.9'
        ];

        $this
            ->actingAs($this->user, 'api')
            ->postJson('/api/v1/api-key', $payload, [
                'X-Tenant' => $this->tenant->getKey(),
                'X-Subdomain' => $this->tenant->primary_domain->getKey(),
                'Authorization' => 'Bearer ' . $this->user->createToken('test', [
                        'apikey'
                    ])->plainTextToken
            ])->assertStatus(201);

        $this->assertDatabaseHas('api_keys', [
            'name' => $payload['name'],
            'tenant_id' => $this->tenant->id,
            'domain_id' => $this->tenant->primary_domain->getKey()
        ]);

        $this->assertDatabaseHas('model_has_permissions', [
            'model_id' => ApiKey::where('name', $payload['name'])->first()->getKey(),
            'permission_id' => $payload['permissions'][0]
        ]);
    }

    public function test_fetch_api_keys()
    {
        ApiKey::factory()->create([
            'tenant_id' => $this->tenant->getKey(),
            'domain_id' => $this->tenant->primary_domain->getKey()
        ]);

        $otherTenant = $this->createTenant();

        ApiKey::factory()->create([
            'tenant_id' => $otherTenant->getKey(),
            'domain_id' => $otherTenant->primary_domain->getKey()
        ]);

        $response = $this
            ->actingAs($this->user, 'api')
            ->getJson('/api/v1/api-key', [
                'X-Tenant' => $this->tenant->getKey(),
                'X-Subdomain' => $this->tenant->primary_domain->getKey(),
                'Authorization' => 'Bearer ' . $this->user->createToken('test')->plainTextToken,
            ])
            ->assertStatus(200)
            ->assertJsonCount(1, 'data');

        $this->assertArrayHasKey('data', $response->json());
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_delete_api_key()
    {
        $user = $this->tenant->teams->first()->users->first();
        $apiKeyName = $this->faker->name;

        $apiKeyRecord = ApiKey::factory()->create([
            'key' => config('hashing.prefix') . hash_hmac('ripemd160', Str::random(40), $this->tenant->id),
            'name' => $apiKeyName,
            'tenant_id' => $this->tenant->getKey(),
            'domain_id' => $this->tenant->primary_domain->getKey()
        ]);

        $response = $this
            ->actingAs($user, 'api')
            ->deleteJson('/api/v1/api-key/' . $apiKeyRecord->getKey(), [], [
                'X-Tenant' => $this->tenant->getKey(),
                'Authorization' => 'Bearer ' . $user->createToken('test')->plainTextToken,
            ]);

        $response->assertStatus(200);
        $this->assertSoftDeleted('api_keys', [
            'id' => $apiKeyRecord->getKey(),
        ]);
    }
}
