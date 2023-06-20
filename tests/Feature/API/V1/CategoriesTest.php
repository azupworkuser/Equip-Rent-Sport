<?php

namespace Tests\Feature\API\V1;

use App\Models\Asset;
use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoriesTest extends TestCase
{
    use DatabaseMigrations;
    use WithFaker;

    /**
     * @dataProvider provideProductCategoryData
     */
    public function test_if_category_can_be_created_for_an_asset($payload, $statusCode)
    {
        $this
            ->actingAs($this->user, 'api')
            ->postJson('/api/v1/categories', $payload)
            ->assertStatus($statusCode);

        if ($statusCode >= 200 && $statusCode < 204) {
            $this->assertDatabaseHas('categories', $payload);
        }
    }

    public function test_if_particular_category_can_be_returned()
    {
        $asset = Asset::factory()->create([
            'tenant_id' => $this->tenant->getKey(),
            'domain_id' => $this->domain->getKey(),
            'created_by' => $this->user->getKey(),
        ]);

        $category = $asset->categories->first();

        $response = $this
            ->shouldPassToken()
            ->getJson('/api/v1/categories/' . $category->getKey() . '?type=asset&include=assets')
            ->assertStatus(200)
            ->json();

        $this->assertEquals($category->name, $response['data']['name']);
    }

    public function test_if_unique_category_name_works_properly()
    {
        $category = Category::factory()->create([
            'tenant_id' => $this->tenant->getKey(),
            'domain_id' => $this->domain->getKey(),
            'created_by' => $this->user->getKey(),
        ]);

        $payload = [
            'name' => $category->name,
            'description' => '',
            'type' => 'product',
        ];

        $this
            ->actingAs($this->user, 'api')
            ->postJson('/api/v1/categories', $payload, [
                'X-Tenant' => $this->tenant->getKey(),
                'X-Subdomain' => $this->domain->getKey(),
                'Authorization' => 'Bearer ' . $this->user->createToken('test')->plainTextToken,
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrorFor('name');
    }

    public function provideProductCategoryData()
    {
        return [
            [
                [
                    'name' => 'Test Category',
                    'description' => 'Test Description',
                    'type' => 'product',
                ],
                201,
            ],
            [
                [
                    'name' => '',
                    'description' => 'Test Description',
                    'type' => 'product',
                ],
                422,
            ],
        ];
    }

    public function test_if_category_can_be_deleted()
    {
        $category = Category::factory()->create([
            'tenant_id' => $this->tenant->getKey(),
            'domain_id' => $this->domain->getKey(),
            'created_by' => $this->user->getKey(),
        ]);

        $this
            ->actingAs($this->user, 'api')
            ->deleteJson('api/v1/categories/' . $category->getKey(), [], [
                'X-Tenant' => $this->tenant->getKey(),
                'X-Subdomain' => $this->domain->getKey(),
                'Authorization' => 'Bearer ' . $this->user->createToken('test')->plainTextToken,
            ])
            ->assertStatus(200);

        $this->assertSoftDeleted('categories', [
            'id' => $category->getKey(),
        ]);
    }

    public function test_if_Category_can_be_updated()
    {
        $category = Category::factory()->create([
            'tenant_id' => $this->tenant->getKey(),
            'domain_id' => $this->domain->getKey(),
            'created_by' => $this->user->getKey(),
        ]);

        $payload = $category->only('name', 'description', 'type');

        $payload['name'] = $this->faker->name;

        $this
            ->actingAs($this->user, 'api')
            ->putJson('api/v1/categories/' . $category->getKey(), $payload)
            ->assertStatus(200);

        $this->assertDatabaseMissing('categories', $category->only('name'));
        $this->assertDatabaseHas('categories', $payload);
    }

    public function test_if_all_categories_are_returned()
    {
        $category = Category::factory()->create([
            'tenant_id' => $this->tenant->getKey(),
            'domain_id' => $this->domain->getKey(),
            'created_by' => $this->user->getKey(),
        ]);

        $asset = Asset::factory()->create([
            'tenant_id' => $this->tenant->getKey(),
            'domain_id' => $this->domain->getKey(),
            'created_by' => $this->user->getKey(),
        ]);

        $asset->categories()->attach($category);

        $response = $this
            ->shouldPassToken()
            ->getJson('/api/v1/categories?include=assets&type=asset')
            ->assertStatus(200);

        $this->assertEquals($category->getKey(), $response->json('data.0.id'));
    }
}
