<?php

namespace Tests\Feature\API\V1;

use App\Models\Product;
use App\Models\ProductLocation;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ProductLocationTest extends TestCase
{
    use DatabaseMigrations;

    public function test_if_product_location_can_be_created(): void
    {
        $product = Product::factory()->create([
            'tenant_id' => $this->tenant->getKey(),
            'domain_id' => $this->domain->getKey(),
            'created_by' => $this->user->getKey(),
        ]);

        $payload = ProductLocation::factory()->make();

        $response = $this
            ->shouldPassToken()
            ->postJson('/api/v1/product/' . $product->getKey() . '/location', $payload->toArray())
            ->assertSuccessful();

        $this->assertDatabaseHas('product_locations', array_merge([
            'product_id' => $product->getKey(),
        ], $payload->only('address_1', 'city', 'state', 'country', 'postal_code', 'address_type')));
    }

    public function test_if_product_location_can_be_fetched()
    {
        $product = Product::factory()->create([
            'tenant_id' => $this->tenant->getKey(),
            'domain_id' => $this->domain->getKey(),
            'created_by' => $this->user->getKey(),
        ]);

        ProductLocation::factory()->create([
            'product_id' => $product->getKey(),
            'tenant_id' => $this->tenant->getKey(),
            'domain_id' => $this->domain->getKey(),
        ]);

        $this
            ->shouldPassToken()
            ->getJson('/api/v1/product/' . $product->getKey() . '/location')
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'address_1',
                        'address_2',
                        'city',
                        'state',
                        'country',
                        'postal_code',
                        'created_at',
                        'address_type'
                    ],
                ],
            ]);
    }
}
