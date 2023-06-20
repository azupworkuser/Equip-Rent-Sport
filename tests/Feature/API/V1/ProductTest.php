<?php

namespace Tests\Feature\API\V1;

use App\Models\Asset;
use App\Models\Product;
use App\Models\ProductAvailability;
use App\Models\ProductAvailabilitySlot;
use App\Models\ProductInventory;
use App\Models\ProductLocation;
use App\Models\ProductPricingStructure;
use App\Models\ProductType;
use App\Models\States\ProductAvailabilitySlot\Hold;
use App\Models\Unit;
use App\CoreLogic\States\Product\Active;
use App\CoreLogic\States\Product\Disabled;
use Arr;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use DatabaseMigrations;
    use WithFaker;

    public function test_if_a_product_can_be_created()
    {
        $payload = [
            'name' => 'Test Product',
            'description' => 'Test Product Description',
            'visibility' => Product::VISIBILITY_TYPE['Everyone'],
            'code' => 'TESTCODE',
            'advertised_price' => 1000,
            'terms_and_conditions' => 'Test Terms and Conditions',
            'product_type_id' => ProductType::factory()->create()->getKey(),
            'status' => Disabled::class
        ];

        $this
            ->shouldPassToken()
            ->postJson('/api/v1/product', $payload)
            ->assertSuccessful();

        $this->assertDatabaseHas('products', $payload);
    }

    public function test_if_products_can_be_returned()
    {
        $product = Product::factory()->create([
            'tenant_id' => $this->tenant->getKey(),
            'domain_id' => $this->domain->getKey(),
        ]);

        $this
            ->getJson('/api/v1/product/' . $product->getKey())
            ->assertSuccessful()
            ->assertJsonFragment([
                'name' => $product->name,
                'description' => $product->description,
                'visibility' => $product->visibility,
                'advertised_price' => $product->advertised_price,
                'terms_and_conditions' => $product->terms_and_conditions,
                'status' => 'Active'
            ]);
    }

    public function test_product_location_is_saved()
    {
        $product = Product::factory()->create([
            'tenant_id' => $this->tenant->getKey(),
            'domain_id' => $this->domain->getKey(),
            'created_by' => $this->user->getKey(),
        ]);

        $payload = ProductLocation::factory()->make();

        $this
            ->shouldPassToken()
            ->postJson('/api/v1/product/' . $product->getKey() . '/location', $payload->toArray())
            ->assertSuccessful();

        $this->assertDatabaseHas('product_locations', array_merge([
            'product_id' => $product->getKey(),
        ], $payload->only('address_1', 'city', 'state', 'country', 'postal_code', 'address_type')));
    }

    public function test_product_inventory_when_dynamic_value_is_being_Created()
    {
        $product = Product::factory()->create([
            'tenant_id' => $this->tenant->getKey(),
            'domain_id' => $this->domain->getKey(),
            'created_by' => $this->user->getKey(),
        ]);

        $assets = Asset::factory()->count(2)->create([
            'tenant_id' => $this->tenant->getKey(),
            'domain_id' => $this->domain->getKey(),
            'created_by' => $this->user->getKey(),
        ]);

        $payload = [
            'inventory_type' => ProductInventory::INVENTORY_TYPE_DYNAMIC,
            'assets' => $assets->pluck('id')->toArray()
        ];

        $response = $this
            ->shouldPassToken()
            ->postJson('/api/v1/product/' . $product->getKey() . '/product-inventory', $payload)
            ->assertSuccessful();

        $this->assertDatabaseHas('product_inventories', [
            'product_id' => $product->getKey(),
            'inventory_type' => ProductInventory::INVENTORY_TYPE_DYNAMIC,
        ]);

        $this->assertDatabaseHas('asset_product_inventory', [
            'product_inventory_id' => $response->json('inventory.id'),
            'asset_id' => $assets->first()->getKey(),
        ]);
    }

    public function test_if_all_product_inventory_is_returned()
    {
        $product = Product::factory()->create([
            'tenant_id' => $this->tenant->getKey(),
            'domain_id' => $this->domain->getKey(),
            'created_by' => $this->user->getKey(),
        ]);

        $asset = Asset::factory()->create([
            'tenant_id' => $this->tenant->getKey(),
            'domain_id' => $this->domain->getKey(),
            'created_by' => $this->user->getKey(),
        ]);

        $inventory = ProductInventory::factory()->create([
            'product_id' => $product->getKey(),
            'inventory_type' => ProductInventory::INVENTORY_TYPE_DYNAMIC,
            'created_by' => $this->user->getKey(),
            'tenant_id' => $this->tenant->getKey(),
            'domain_id' => $this->domain->getKey(),
        ]);

        $inventory->assets()->attach($asset->getKey());

        $this
            ->getJson('/api/v1/product/' . $product->getKey() . '/product-inventory')
            ->assertSuccessful()
            ->assertJsonFragment([
                'id' => $inventory->getKey(),
                'inventory_type' => ProductInventory::INVENTORY_TYPE_DYNAMIC,
            ]);
    }

    public function test_getting_a_particular_product_inventory()
    {
        $product = Product::factory()->create([
            'tenant_id' => $this->tenant->getKey(),
            'domain_id' => $this->domain->getKey(),
            'created_by' => $this->user->getKey(),
        ]);

        $asset = Asset::factory()->create([
            'tenant_id' => $this->tenant->getKey(),
            'domain_id' => $this->domain->getKey(),
            'created_by' => $this->user->getKey(),
        ]);

        $inventory = ProductInventory::factory()->create([
            'product_id' => $product->getKey(),
            'inventory_type' => ProductInventory::INVENTORY_TYPE_DYNAMIC,
            'created_by' => $this->user->getKey(),
            'tenant_id' => $this->tenant->getKey(),
            'domain_id' => $this->domain->getKey(),
        ]);

        $inventory->assets()->attach($asset->getKey());

        $this
            ->shouldPassToken()
            ->getJson('/api/v1/product/' . $product->getKey() . '/product-inventory/' . $inventory->getKey())
            ->assertSuccessful()
            ->assertJsonFragment([
                'id' => $inventory->getKey(),
                'inventory_type' => ProductInventory::INVENTORY_TYPE_DYNAMIC,
            ]);
    }

    public function test_product_availability_storage()
    {
        $product = Product::factory()->create([
            'tenant_id' => $this->tenant->getKey(),
            'domain_id' => $this->domain->getKey(),
            'created_by' => $this->user->getKey(),
        ]);

        $asset = Asset::factory()->create([
            'tenant_id' => $this->tenant->getKey(),
            'domain_id' => $this->domain->getKey(),
            'created_by' => $this->user->getKey(),
            'quantity' => 2,
            'capacity_per_quantity' => 1
        ]);

        $inventory = ProductInventory::factory()->create([
            'product_id' => $product->getKey(),
            'inventory_type' => ProductInventory::INVENTORY_TYPE_DYNAMIC,
            'created_by' => $this->user->getKey(),
            'tenant_id' => $this->tenant->getKey(),
            'domain_id' => $this->domain->getKey(),
        ]);

        $inventory->assets()->attach($asset->getKey());

        $payload = [
            'name' => $this->faker->words(3, true),
            'duration' => 1, // hour
            'duration_type' => 'hour',
            'start_date' => Carbon::now()->addDays(1)->format('Y-m-d H:i:s'),
            'end_date' => Carbon::now()->addDays(2)->format('Y-m-d H:i:s'),
            'start_time' => Carbon::now()->format('G:i'),
            'end_time' => Carbon::now()->addHours(8)->format('G:i'),
            'increment' => 1, // hours
            'increment_type' => 'hour',
            'available_days' => [1, 2, 3, 4, 5]
        ];

        $this
            ->shouldPassToken()
            ->postJson('/api/v1/product/' . $product->getKey() . '/availability', $payload)
            ->assertSuccessful();

        $payload['available_days'] = json_encode($payload['available_days']);
        $payload['start_time'] = Carbon::parse($payload['start_time']);
        $payload['end_time'] = Carbon::parse($payload['end_time']);

        $this->assertDatabaseHas('product_availabilities', $payload);
    }

    public function test_if_availability_can_be_retrieved()
    {
        $product = Product::factory()->create([
            'tenant_id' => $this->tenant->getKey(),
            'domain_id' => $this->domain->getKey(),
            'created_by' => $this->user->getKey(),
        ]);

        $asset = Asset::factory()->create([
            'tenant_id' => $this->tenant->getKey(),
            'domain_id' => $this->domain->getKey(),
            'created_by' => $this->user->getKey(),
        ]);

        $inventory = ProductInventory::factory()->create([
            'product_id' => $product->getKey(),
            'inventory_type' => ProductInventory::INVENTORY_TYPE_DYNAMIC,
            'created_by' => $this->user->getKey(),
            'tenant_id' => $this->tenant->getKey(),
            'domain_id' => $this->domain->getKey(),
        ]);

        $inventory->assets()->attach($asset->getKey());

        $availability = ProductAvailability::factory()->create([
            'product_id' => $product->getKey(),
            'created_by' => $this->user->getKey(),
            'tenant_id' => $this->tenant->getKey(),
            'domain_id' => $this->domain->getKey(),
        ]);

        $this
            ->shouldPassToken()
            ->getJson('/api/v1/product/' . $product->getKey() . '/availability')
            ->assertSuccessful()
            ->assertJsonFragment([
                'id' => $availability->getKey(),
                'name' => $availability->name,
            ]);
    }

    public function test_product_availability_creation_slots()
    {
        $product = Product::factory()->create([
            'tenant_id' => $this->tenant->getKey(),
            'domain_id' => $this->domain->getKey(),
            'created_by' => $this->user->getKey(),
        ]);

        $asset = Asset::factory()->create([
            'tenant_id' => $this->tenant->getKey(),
            'domain_id' => $this->domain->getKey(),
            'created_by' => $this->user->getKey(),
            'capacity_per_quantity' => 2,
            'quantity' => 2,
        ]);

        $inventory = ProductInventory::factory()->create([
            'product_id' => $product->getKey(),
            'inventory_type' => ProductInventory::INVENTORY_TYPE_DYNAMIC,
            'created_by' => $this->user->getKey(),
            'tenant_id' => $this->tenant->getKey(),
            'domain_id' => $this->domain->getKey(),
        ]);

        $inventory->assets()->attach($asset->getKey());

        $payload = [
            'name' => $this->faker->words(3, true),
            'duration' => 1, // hour
            'duration_type' => 'hour',
            'start_date' => Carbon::now()->addDays(1)->format('Y-m-d 09:00:00'),
            'end_date' => Carbon::now()->addDays(3)->format('Y-m-d 17:00:00'),
            'start_time' => "9:00",
            'end_time' => '17:00',
            'increment' => 1, // hours
            'increment_type' => 'hour',
            'available_days' => [1, 2, 3, 4, 5]
        ];

        $response = $this
            ->shouldPassToken()
            ->postJson('/api/v1/product/' . $product->getKey() . '/availability/', $payload)
            ->assertSuccessful();

        $availability = ProductAvailability::find($response->json('id'));

        $payload['available_days'] = json_encode($payload['available_days']);
        $payload['start_time'] = Carbon::parse($payload['start_time']);
        $payload['end_time'] = Carbon::parse($payload['end_time']);

        $this->assertDatabaseHas('product_availabilities', $payload);

        $slots = $availability->slots;
        $this->assertCount(96, $slots); // 8 hours per day +  3 days + (2 Quantity * 2 Capacity per Quantity) = 96
    }

    public function test_if_an_query_can_be_made_for_a_particular_date()
    {
        $product = Product::factory()->create([
            'tenant_id' => $this->tenant->getKey(),
            'domain_id' => $this->domain->getKey(),
            'created_by' => $this->user->getKey(),
        ]);

        $asset = Asset::factory()->create([
            'tenant_id' => $this->tenant->getKey(),
            'domain_id' => $this->domain->getKey(),
            'created_by' => $this->user->getKey(),
            'capacity_per_quantity' => 2,
            'quantity' => 2,
        ]);

        $inventory = ProductInventory::factory()->create([
            'product_id' => $product->getKey(),
            'inventory_type' => ProductInventory::INVENTORY_TYPE_DYNAMIC,
            'created_by' => $this->user->getKey(),
            'tenant_id' => $this->tenant->getKey(),
            'domain_id' => $this->domain->getKey(),
        ]);

        $inventory->assets()->attach($asset->getKey());

        $payload = [
            'name' => $this->faker->words(3, true),
            'duration' => 1, // hour
            'duration_type' => 'hour',
            'start_date' => Carbon::now()->addDays(1)->format('Y-m-d 09:00:00'),
            'end_date' => Carbon::now()->addDays(3)->format('Y-m-d 17:00:00'),
            'start_time' => "9:00",
            'end_time' => '17:00',
            'increment' => 1, // hours
            'increment_type' => 'hour',
            'available_days' => [1, 2, 3, 4, 5]
        ];

        $response = $this
            ->shouldPassToken()
            ->postJson('/api/v1/product/' . $product->getKey() . '/availability/', $payload)
            ->assertSuccessful();

//        STOP 0
//        dd($response->json());
//        dd(ProductAvailability::find($response->json('id'))->slots->toArray());
        $this->assertCount(96, ProductAvailability::find($response->json('id'))->slots);

        $slotResponse = $this
            ->shouldPassToken()
            ->postJson('/api/v1/product/' . $product->getKey() . '/availability/check?date=' . now()->addDay()->format('Y-m-d'), $payload)
            ->assertSuccessful();

        // STOP 1
        $slots = array_keys($slotResponse->json());
//        dd($slots); // list of slots
//        dd($slotResponse->json()); // list of slots with availability
        $this->assertEquals(4, $slotResponse->json($slots[0])['available']);

        $holdResponse = $this
            ->shouldPassToken()
            ->postJson('/api/v1/product/' . $product->getKey() . '/availability/hold?slot=' . Arr::first($slots))
            ->assertSuccessful();

        // STOP 2
//        dd($holdResponse->json());
        $this->assertDatabaseHas('product_availability_slots', [
            'product_availability_id' => $response->json('id'),
            'status' => Hold::class,
            'tenant_id' => $this->tenant->getKey(),
        ]);

        $slotAfterHoldResponse = $this
            ->shouldPassToken()
            ->postJson('/api/v1/product/' . $product->getKey() . '/availability/check?date=' . now()->addDay()->format('Y-m-d'), $payload)
            ->assertSuccessful();

        // STOP 3
//        dd($slotAfterHoldResponse->json());
        $this->assertEquals(3, $slotAfterHoldResponse->json($slots[0])['available']);

        $anotherHoldResponse = $this
            ->shouldPassToken()
            ->postJson('/api/v1/product/' . $product->getKey() . '/availability/hold?slot=' . Arr::first($slots))
            ->assertSuccessful();

        // STOP 4
//        dd($anotherHoldResponse->json());
        $this->assertDatabaseHas('product_availability_slots', [
            'product_availability_id' => $response->json('id'),
            'status' => Hold::class,
            'start_at' => Arr::first($slots),
            'tenant_id' => $this->tenant->getKey(),
        ]);

        $slots = array_keys($slotResponse->json());

        $slotAfterHoldResponse = $this
            ->shouldPassToken()
            ->postJson('/api/v1/product/' . $product->getKey() . '/availability/check?date=' . now()->addDay()->format('Y-m-d'), $payload)
            ->assertSuccessful();

        // STOP 5
//        dd($slotAfterHoldResponse->json());
        $this->assertEquals(2, $slotAfterHoldResponse->json($slots[0])['available']);
    }
}
