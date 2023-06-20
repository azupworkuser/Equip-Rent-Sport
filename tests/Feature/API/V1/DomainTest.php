<?php

namespace Tests\Feature\API\V1;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DomainTest extends TestCase
{
    use DatabaseMigrations;
    use WithFaker;

    /**
     * @dataProvider provideTenantData
     */
    public function test_if_tenant_can_be_updated($payload, $statusCode, $passToken)
    {
        $this
            ->shouldPassToken($passToken)
            ->putJson('/api/v1/domain/' . $this->domain->getKey() . '/profile', $payload)
            ->assertStatus($statusCode);

        if ($statusCode === 200) {
            $this->assertDatabaseHas('domains', $payload);
        }
    }

    public function provideTenantData(): array
    {
        return [
            [
                [
                    'location_name' => 'test location name',
                    'company' => 'Test Company',
                    'website' => 'https://test.com',
                    'primary_industry' => 'tours',
                    'address_1' => 'Test Address 1',
                    'address_2' => 'Test Address 2',
                    'city' => 'Test City',
                    'state' => 'Test State',
                    'zip' => 'Test Zip',
                    'country' => 'US',
                    'phone' => '8888888888',
                    'dial_code' => '123'
                ],
                200,
                true
            ],
            [
                [
                    'location_name' => 'test location name',
                    'website' => 'https://test.com',
                    'primary_industry' => 'tours',
                    'address_1' => 'Test Address 1',
                    'address_2' => 'Test Address 2',
                    'city' => 'Test City',
                    'state' => 'Test State',
                    'zip' => 'Test Zip',
                    'country' => 'US',
                    'phone' => '8888888888',
                    'dial_code' => '123'
                ],
                401,
                false
            ],
        ];
    }

    /**
     * @dataProvider provideRegionalData
     */
    public function test_if_regional_settings_can_be_updated($payload, $statusCode, $passToken = true)
    {
        $token = $passToken ? 'Bearer ' . $this->user->createToken('test')->plainTextToken : null;

        $this
            ->actingAs($this->user, 'api')
            ->putJson('/api/v1/domain/' . $this->domain->getKey() . '/region', $payload, [
                'X-Tenant' => $this->tenant->getKey(),
                'X-Subdomain' => $this->domain->getKey(),
                'Authorization' => $token
            ])
            ->assertStatus($statusCode);

        if ($statusCode === 200) {
            $this->assertDatabaseHas('domains', $payload);
        }
    }

    public function provideRegionalData()
    {
        return [
            [
                [
                    'language_iso2' => 'en',
                    'timezone_iso3' => 'EST',
                    'currency_iso3' => 'USD',
                    'order_number_format' => 'alphanumeric',
                    'order_number_prefix' => 'TEST',
                    'date_format' => 'Y-m-d',
                    'time_format' => '12h'
                ],
                200,
            ],
            [
                [
                    'language_iso2' => 'en',
                    'timezone_iso3' => 'EST',
                    'currency_iso3' => 'USD',
                    'order_number_format' => 'alphanumeric',
                    'order_number_prefix' => 'TEST',
                    'date_format' => 'Y-m-d',
                    'time_format' => '12h'
                ],
                401,
                false
            ]

        ];
    }

    public function test_if_all_domain_details_can_be_returned()
    {
        $this->domain->update($payload = [
            'language_iso2' => 'en',
            'timezone_iso3' => 'EST',
            'currency_iso3' => 'USD',
            'order_number_format' => 'alphanumeric',
            'order_number_prefix' => 'TEST',
            'date_format' => 'Y-m-d',
            'time_format' => '12h'
        ]);

        $this
            ->actingAs($this->user, 'api')
            ->getJson('/api/v1/domain/' . $this->domain->getKey(), [
                'X-Tenant' => $this->tenant->getKey(),
                'X-Subdomain' => $this->domain->getKey(),
                'Authorization' => 'Bearer ' . $this->user->createToken('test')->plainTextToken
            ])
            ->assertStatus(200)
            ->assertJsonFragment($payload);
    }

    public function test_If_all_domains_are_returned_for_a_tenant()
    {
        $this->createTenant(); // just to create another subdomain

        $response = $this
            ->actingAs($this->user, 'api')
            ->getJson('/api/v1/domain/', [
                'X-Tenant' => $this->tenant->getKey(),
                'X-Subdomain' => $this->domain->getKey(),
                'Authorization' => 'Bearer ' . $this->user->createToken('test')->plainTextToken
            ])
            ->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonFragment([
                'id' => $this->domain->getKey()
            ]);
    }

    public function test_if_business_schedule_updates()
    {
        $payload = [
            'week_starts_on' => 'monday',
            'business_hours' => [
                [
                    'day' => 'monday',
                    'open_time' => '09:00:00',
                    'close_time' => '17:00:00'
                ],
                [
                    'day' => 'tuesday',
                    'open_time' => '09:00:00',
                    'close_time' => '17:00:00'
                ],
            ]
        ];

        $this
            ->putJson('/api/v1/domain/' . $this->domain->getKey() . '/schedule', $payload, [
                'X-Tenant' => $this->tenant->getKey(),
                'X-Subdomain' => $this->domain->getKey(),
                'Authorization' => 'Bearer ' . $this->user->createToken('test')->plainTextToken
            ])
            ->assertStatus(200);

        $domain = $this->domain->fresh();
        $this->assertEquals($payload['week_starts_on'], $domain->data['week_starts_on']);
        $this->assertEquals($payload['business_hours'], $domain->data['business_hours']);
    }
    public function test_if_new_domain_created()
    {
        $payload = [
            'domain' => uniqid('domain-', true),
            'location_name' => $this->faker->name()
        ];

        $this
            ->postJson('/api/v1/domain/store', $payload, [
                'X-Tenant' => $this->tenant->getKey(),
                'X-Subdomain' => $this->domain->getKey(),
                'Authorization' => 'Bearer ' . $this->user->createToken('test', [
                    'domain.create',
                ])->plainTextToken
            ])
            ->assertStatus(201);

        $this->assertDatabaseHas('domains', [
            'domain' => $payload['domain'],
            'location_name' => $payload['location_name'],
            'tenant_id' => $this->tenant->getKey()
        ]);
    }
    public function test_if_domain_name_is_missing()
    {
        $payload = [
            'location_name' => $this->faker->name()
        ];
        $this
            ->postJson('/api/v1/domain/store', $payload, [
                'X-Tenant' => $this->tenant->getKey(),
                'X-Subdomain' => $this->domain->getKey(),
                'Authorization' => 'Bearer ' . $this->user->createToken('test', [
                    'domain.create',
                ])->plainTextToken
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'domain'
            ]);
    }
}
