<?php

namespace Tests\Feature\API\SimulateScenarios;

use App\Models\AvailabilitySession;
use App\Models\Domain;
use App\Models\Product;
use App\Models\ProductAvailability;
use App\Models\ProductOptionAvailabilityEvent;
use App\Models\ProductPricing;
use App\Models\Tenant;
use App\Models\User;
use App\CoreLogic\Services\Availabilities\DynamicAvailability;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class CreateBookingTest extends TestCase
{
    use DatabaseMigrations;

    public function test_if_a_booking_can_be_created()
    {
        $this->markTestSkipped('This test is not yet implemented');
        /**
         * Case
         * 1. John wants to book jetski
         * 2. Wants to book Jetski for 1 hour on tomorrow
         */

        $product = Product::factory()->create([
            'tenant_id' => $this->tenant->getKey(),
            'domain_id' => $this->domain->getKey()
        ]);

        $availability = ProductAvailability::factory()->create([
            'starts_at'  => now()->format('Y-m-d 00:00:00'),
            'ends_at' => now()->addDays(10)->format('Y-m-d 23:59:59'),
            'product_id' => $product->getKey(),
            'tenant_id' => $product->tenant_id,
            'domain_id' => $product->domain_id,
            'created_by' => $product->created_by,
            'type' => DynamicAvailability::class
        ]);

        ProductPricing::factory()->create([
            'product_id' => $product->getKey(),
            'tenant_id' => $product->tenant_id,
            'domain_id' => $product->domain_id,
        ]);

        // makes a request to check tomorrow's date for a jetski on product page
        $response = $this
            ->getJson(
                '/api/v1/product/' . $product->getKey() . '/availability?date=' . now()->format('Y-m-d'),
            )
            ->assertSuccessful();

        $response->assertJsonStructure([
            'data' => [
                '*' => [ // time slot for the day
                    '*' => [ // id of availability booking session
                        'available',
                        'vacancies',
                        'total'
                    ]
                ]
            ]
        ]);
        $availabilityResponse = $response->json('data');

        $keys = array_keys($availability);
        $availability = $availabilityResponse[$keys[0]];

        $bookingResponse = $this
            ->postJson(
                '/api/v1/booking/' . $product->getKey() . '/Availability/' . array_keys($availability)[0]
            )
            ->assertSuccessful();

        $this->assertDatabaseHas('bookings', [
            'product_availability_id' => $availability->getKey()
        ]);
    }
}
