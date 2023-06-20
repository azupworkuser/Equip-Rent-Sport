<?php

namespace Tests\Feature\API\V1;

use App\Models\Asset;
use App\Models\Category;
use App\Models\States\AssetStates\EnabledState;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class AssetTest extends TestCase
{
    use DatabaseMigrations;
    use WithFaker;

    public function test_view_all_assets_for_a_tenant()
    {
        Asset::factory()->count($tenantAssetCount = 3)->create([
            'tenant_id' => $this->tenant->getKey(),
            'domain_id' => $this->domain->getKey(),
            'created_by' => $this->user->getKey(),
        ]);

        Asset::factory()->count(4)->create(); // random assets

        $expectedStructure = [
            'data' => [
                    '*' => [
                        'name',
                        'quantity',
                        'capacity_per_quantity',
                        'total_capacity',
                        'shared_between_products',
                        'shared_between_bookings',
                        'created_at',
                        'status',
                        'categories' => [
                            '*' => [
                                'created_at',
                                'id',
                                'name',
                            ],
                        ],
                    ],
                ],
                'links' => [
                    'first',
                    'last',
                    'prev',
                    'next',
                ],
                'meta' => [
                    'current_page',
                    'from',
                    'last_page',
                    'links',
                    'path',
                    'per_page',
                    'to',
                    'total',
                ],
            ];

        $response = $this
            ->getJson('/api/v1/asset')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount($tenantAssetCount, 'data')
            ->assertJsonStructure($expectedStructure);

        Asset::factory()->create([
            'tenant_id' => $this->tenant->getKey(),
            'domain_id' => $this->domain->getKey(),
            'created_by' => $this->user->getKey(),
            'status' => EnabledState::class,
            'name' => 'This is a enabled asset'
        ]);

        $this
            ->getJson('/api/v1/asset?filter[name]=This is a enabled asset')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(1, 'data');

        $this
            ->getJson('/api/v1/asset?filter[name]=This is a enabled asset')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(1, 'data');
    }

    /**
     * @dataProvider provideAssetData
     */
    public function test_if_asset_can_be_created($payload, $statusCode, $permissionName, $passToken = true)
    {
        $category = Category::factory()->create([
            'tenant_id' => $this->tenant->getKey(),
            'domain_id' => $this->domain->getKey(),
            'created_by' => $this->user->getKey(),
            'type' => 'asset'
        ]);

        $payload['category_ids'] = [$category->getKey()];

        $this
            ->actingAs($this->user, 'api')
            ->shouldPassToken($passToken, [$permissionName])
            ->postJson('/api/v1/asset', $payload, [])
            ->assertStatus($statusCode);

        if ($statusCode < 205) {
            $this->assertEquals($category->getKey(), Asset::first()->categories->first()->getKey());

            unset($payload['category_ids']);
            $this->assertDatabaseHas('assets', $payload);
        }
    }

    /**
     * @dataProvider provideCategoriesData
     */
    public function test_if_categories_can_be_filtered_for_asset($payload, $statusCode, $permissionName, $passToken = true)
    {
        $category = Category::factory()->create(array_merge($payload, [
            'tenant_id' => $this->tenant->getKey(),
            'domain_id' => $this->domain->getKey(),
            'created_by' => $this->user->getKey(),
        ]));

        $response = $this
            ->actingAs($this->user, 'api')
            ->shouldPassToken($passToken)
            ->getJson('/api/v1/categories?filter[type]=asset')
            ->assertStatus($statusCode);

        if ($statusCode < 205) {
            $this->assertEquals($category->getKey(), $response->json('data.0.id'));
        }
    }

    public function test_if_a_particular_asset_can_be_fetched()
    {
        $asset = Asset::factory()->create([
            'tenant_id' => $this->tenant->getKey(),
            'domain_id' => $this->domain->getKey(),
            'created_by' => $this->user->getKey(),
        ]);

        $response = $this
            ->shouldPassToken()
            ->getJson('/api/v1/asset/' . $asset->getKey())
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'quantity',
                'capacity_per_quantity',
                'total_capacity',
                'shared_between_products',
                'shared_between_bookings',
                'created_at',
                'status',
                'categories' => [
                    '*' => [
                        'created_at',
                        'id',
                        'name',
                    ],
                ],
            ]);
    }

    public function provideCategoriesData()
    {
        return [
            [
                [
                    'name' => 'Test category',
                    'type' => 'asset',
                ],
                200,
                '*',
                true
            ]
        ];
    }

    public function provideAssetData()
    {
        return [
            [
                [
                    'name' => 'Test Asset',
                    'quantity' => 10,
                    'capacity_per_quantity' => 10,
                    'shared_between_products' => true,
                    'shared_between_bookings' => true,
                ],
                201,
                'inventory.asset.create',
            ],
            [
                [
                    'name' => 'Test Asset',
                    'quantity' => 10,
                    'capacity_per_quantity' => 10,
                    'shared_between_products' => true,
                    'shared_between_bookings' => true,
                ],
                403,
                '',
            ],
            [
                [
                    'name' => 'Test Asset',
                    'quantity' => 10,
                    'capacity_per_quantity' => 10,
                    'shared_between_products' => true,
                    'shared_between_bookings' => true,
                ],
                401,
                'inventory.asset.create',
                false
            ],
            [
                [
                    'name' => 'Test Asset',
                    'quantity' => 10,
                    'capacity_per_quantity' => 10,
                    'shared_between_products' => true,
                    'shared_between_bookings' => true,
                ],
                401,
                'inventory.asset.create',
                false
            ],
        ];
    }

    public function test_if_name_unique_validation_works()
    {
        $payload = collect(Asset::factory()->raw())->except('tenant_id', 'domain_id', 'created_by')->toArray();

        Asset::factory()->create([
            'name' => $payload['name'],
            'tenant_id' => $this->tenant->getKey(),
            'domain_id' => $this->domain->getKey(),
        ]);

        $this
            ->actingAs($this->user, 'api')
            ->postJson('/api/v1/asset', $payload, [
                'X-Tenant' => $this->tenant->getKey(),
                'X-Subdomain' => $this->domain->getKey(),
                'Authorization' => 'Bearer ' . $this->user->createToken('test', [
                    'inventory.asset.create',
                ])->plainTextToken
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrorFor('name');
    }

    public function test_if_asset_can_be_updated()
    {
        $asset = Asset::factory()->create([
            'tenant_id' => $this->tenant->getKey(),
            'domain_id' => $this->domain->getKey(),
        ]);
        $asset->name = 'Updated Asset Name';

        $this
            ->actingAs($this->user, 'api')
            ->putJson('/api/v1/asset/' . $asset->getKey(), $asset->toArray(), [
                'X-Tenant' => $this->tenant->getKey(),
                'X-Subdomain' => $this->domain->getKey(),
                'Authorization' => 'Bearer ' . $this->user->createToken('test', [
                    'inventory.asset.update',
                ])->plainTextToken
            ])
            ->assertStatus(200);

        $this->assertDatabaseHas('assets', [
            'name' => 'Updated Asset Name',
            'id' => $asset->getKey(),
        ]);
    }

    /**
     * @dataProvider provideAssetDataForUpdate
     */
    public function test_If_update_request_validates_token_and_permission($statusCode, $passToken, $passPermission)
    {
        $asset = Asset::factory()->create([
            'tenant_id' => $this->tenant->getKey(),
            'domain_id' => $this->domain->getKey(),
        ]);
        $asset->name = 'Updated Asset Name';

        $token = null;

        if ($passToken) {
            $token = 'Bearer ' . $this->user->createToken('test', [
                $passPermission ? 'inventory.asset.update' : '',
            ])->plainTextToken;
        }

        $this
            ->putJson('/api/v1/asset/' . $asset->getKey(), $asset->toArray(), [
                'X-Tenant' => $this->tenant->getKey(),
                'X-Subdomain' => $this->domain->getKey(),
                'Authorization' => $passToken ? $token : null
            ])
            ->assertStatus($statusCode);

        $this->assertDatabaseMissing('assets', [
            'name' => 'Updated Asset Name',
            'id' => $asset->getKey(),
        ]);
    }

    public function provideAssetDataForUpdate()
    {
        return [
            [
                403,
                true,
                false
            ],
            [
                401,
                false,
                false
            ]
        ];
    }

    public function test_if_asset_cannot_be_updated_for_a_different_tenant()
    {
        $asset = Asset::factory()->create();
        $asset->name = 'Updated Asset Name';

        $this
            ->actingAs($this->user, 'api')
            ->putJson('/api/v1/asset/' . $asset->getKey(), $asset->toArray(), [
                'X-Tenant' => $this->tenant->getKey(),
                'X-Subdomain' => $this->domain->getKey(),
                'Authorization' => 'Bearer ' . $this->user->createToken('test', [
                        'inventory.asset.update',
                    ])->plainTextToken
            ])
            ->assertStatus(404);

        $this->assertDatabaseMissing('assets', [
            'name' => 'Updated Asset Name',
            'id' => $asset->getKey(),
        ]);
    }

    public function test_if_a_asset_can_be_deleted()
    {
        $asset = Asset::factory()->create([
            'tenant_id' => $this->tenant->getKey(),
            'domain_id' => $this->domain->getKey(),
        ]);

        $this
            ->actingAs($this->user, 'api')
            ->deleteJson('/api/v1/asset/' . $asset->getKey(), $asset->toArray(), [
                'X-Tenant' => $this->tenant->getKey(),
                'X-Subdomain' => $this->domain->getKey(),
                'Authorization' => 'Bearer ' . $this->user->createToken('test', [
                    'inventory.asset.delete',
                ])->plainTextToken
            ])
            ->assertStatus(200);

        $this->assertSoftDeleted('assets', [
            'id' => $asset->getKey(),
        ]);
    }
}
