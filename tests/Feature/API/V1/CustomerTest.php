<?php

namespace Tests\Feature\API\V1;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Customer;

class CustomerTest extends TestCase
{
    use DatabaseMigrations;
    use WithFaker;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_if_customer_can_create()
    {

        $payload = [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->email,
            'phone' => $this->faker->phoneNumber,
            'phone_code' => '+91'
        ];

        $response = $this
            ->actingAs($this->user)
            ->postJson('/api/v1/customers', $payload);

        $response->assertStatus(201);

        $this->assertDatabaseHas('customers', [
            'tenant_id' => $this->tenant->id,
            'first_name' => $payload['first_name']
        ]);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_if_customer_can_delete()
    {
        $customer = Customer::factory()->create([
            'tenant_id' => $this->tenant->getKey()
        ]);

        $this
            ->actingAs($this->user)
            ->deleteJson('/api/v1/customers/' . $customer->getKey())
            ->assertStatus(200);

        $this->assertSoftDeleted($customer);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_if_customer_can_update()
    {
        $customer = Customer::factory()->create([
            'tenant_id' => $this->tenant->getKey()
        ]);

        $request = array_merge($customer->toArray(), [
            'email' => $this->faker->email,
        ]);

        $this
            ->actingAs($this->user)
            ->putJson('/api/v1/customers/' . $customer->getKey(), $request)
            ->assertStatus(200);

        $updatedCustomer = Customer::where("id", $customer->getKey())->first();
        $this->assertSame($request['email'], $updatedCustomer->email, 'Email data not updated!!');
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_if_list_of_all_customers_are_Returned()
    {
        Customer::truncate();
        $customers = Customer::factory()->count(5)->create([
            'tenant_id' => $this->tenant->getKey(),
            'domain_id' => $this->domain->getKey(),
        ]);

        $firstCustomerEmail = $customers->first()->email;

        $this
            ->actingAs($this->user)
            ->getJson('/api/v1/customers')
            ->assertSuccessful()
            ->assertJsonStructure([
                'data'
            ])->assertJsonCount(5, 'data')
            ->assertJsonFragment([
                'email' => $firstCustomerEmail
            ]);
    }

    public function test_customer_view_by_id()
    {
        $customer = Customer::factory()->create([
            'tenant_id' => $this->tenant->getKey()
        ]);

        $this
            ->shouldPassToken()
            ->getJson('/api/v1/customers/' . $customer->getKey())
            ->assertStatus(200)
            ->assertJsonFragment([
                'id' => $customer->getkey()
            ]);
    }

    public function test_customer_view_by_id_negative()
    {
        $this
            ->shouldPassToken()
            ->getJson('/api/v1/customers/random-customer-id-009')
            ->assertStatus(404);
    }

    public function test_search_customer_by_first_name()
    {
        $factory = Customer::factory()->create([
            'tenant_id' => $this->tenant->getKey(),
            'first_name' => 'John'
        ]);

        $this
            ->actingAs($this->user)
            ->getJson('/api/v1/customers?filter[first_name]=' . $factory->first_name)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data'
            ])
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment([
                'first_name' => $factory->first_name
            ]);
    }

    public function test_search_customer_by_keyword()
    {
        $factory = Customer::factory()->create([
            'tenant_id' => $this->tenant->getKey(),
            'email' => 'email@example.com'
        ]);

        $this
            ->actingAs($this->user)
            ->getJson('/api/v1/customers?filter[search_customer]=' . $factory->email)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data'
            ])
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment([
                'first_name' => $factory->first_name
            ]);
    }

    public function test_search_customer_by_first_name_negative()
    {
        Customer::factory()->create([
            'tenant_id' => $this->tenant->getKey(),
            'first_name' => 'John'
        ]);

        $this
            ->actingAs($this->user)
            ->getJson('/api/v1/customers?filter[first_name]=TestNancy')
            ->assertStatus(200)
            ->assertJsonStructure([
                'data'
            ])
            ->assertJsonCount(0, 'data');
    }

    public function test_if_all_the_required_fields_are_empty()
    {
        $this
            ->postJson('/api/v1/customers', [
                'first_name' => null,
                'last_name' =>  null,
                'email' => null,
                'phone' => null,
                'phone_code' => null
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'first_name',
                'last_name',
                'email',
                'phone',
                'phone_code'
            ]);
    }

     /**
     * @dataProvider provideCustomerFilterData
     */
    public function test_if_customer_list_works_with_filters($filterValues, $expectedCount)
    {
        Customer::factory()->count(2)->create([
            'first_name' => 'Test First Name',
            'last_name' => 'ABC123',
            'tenant_id' => $this->tenant->getKey(),
            'domain_id' => $this->domain->getKey(),
        ]);
        Customer::factory()->count(10)->create([
            'first_name' => 'SOMETHING ELSE',
            'country' => 'india',
            'tenant_id' => $this->tenant->getKey(),
            'domain_id' => $this->domain->getKey(),
        ]);
        Customer::factory()->count(10)->create();
        $filters = http_build_query([
            'filter' => $filterValues
        ]);
        $this
            ->getJson('/api/v1/customers?' . $filters)
            ->assertStatus(200)
            ->assertJsonCount($expectedCount, 'data');
    }

    public function provideCustomerFilterData()
    {
        return [
            [
                [
                    'first_name' => 'Test First Name',
                ],
                2
            ], [
                [
                    'country' => 'india',
                ],
                10
            ], [
                [
                    'first_name' => 'SOMETHING',
                ],
                10
            ], [
                [
                    'email' => 'test@test.com',
                ],
                0
            ], [
                [
                    'first_name' => 'something that doesn\'t exists',
                ],
                0
            ],
        ];
    }
    /**
     * @dataProvider providePromocodeSortingData
     */
    public function test_if_customer_list_works_with_sorting($sortingValues, $expectedFirstTitle)
    {

        Customer::factory()->count(1)->create([
            'first_name' => 'C listing',
            'country' => 'C',
            'last_name' => 'CL',
            'tenant_id' => $this->tenant->getKey()
        ]);

        Customer::factory()->count(1)->create([
            'first_name' => 'A listing',
            'country' => 'A',
            'last_name' => 'AL',
            'tenant_id' => $this->tenant->getKey()
        ]);

        Customer::factory()->count(1)->create([
            'first_name' => 'B listing',
            'country' => 'B',
            'last_name' => 'BL',
            'tenant_id' => $this->tenant->getKey()
        ]);

        Customer::factory()->count(10)->create();

        $filters = http_build_query([
            'sort' => $sortingValues
        ]);

        $response = $this
            ->getJson('/api/v1/customers?' . $filters)
            ->assertStatus(200);
        $this->assertEquals($expectedFirstTitle, $response->json('data.0.first_name'));
    }

    public function providePromocodeSortingData()
    {
        return [
            [
                [
                    'first_name'
                ],
                'A listing',
            ],
            [
                [
                    '-first_name'
                ],
                'C listing',
            ],
            [
                [
                    '-country'
                ],
                'C listing',
            ],
            [
                [
                    'country'
                ],
                'A listing',
            ],
        ];
    }
}
