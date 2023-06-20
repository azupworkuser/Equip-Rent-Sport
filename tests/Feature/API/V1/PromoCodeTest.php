<?php

namespace Tests\Feature\API\V1;

use App\Models\Offer;
use App\Models\Product;
use App\CoreLogic\Enum\Offer\OfferTypeEnum;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\QueryBuilder\QueryBuilder;
use Tests\TestCase;

class PromoCodeTest extends TestCase
{
    use DatabaseMigrations;
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $this->product = Product::factory()->create([
            'tenant_id' => $this->tenant->getKey(),
            'domain_id' => $this->domain->getKey(),
        ]);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_if_promo_code_can_be_deleted()
    {
        $this->withoutExceptionHandling();
        $title = $this->faker->name;
        $promoCode = Offer::factory()->create([
            'tenant_id' => $this->tenant->getKey(),
            'created_by' => $this->user->getKey(),
            'domain_id' => $this->tenant->primary_domain->getKey(),
            'title' => $title,
            'offer_type' => OfferTypeEnum::PROMO_CODE
        ]);

        $this
            ->actingAs($this->user, 'api')
            ->deleteJson('/api/v1/offers/' . $promoCode->getKey(), [], [
                'X-Tenant' => $this->tenant->getKey(),
                'Authorization' => 'Bearer ' . $this->user->createToken('test', [
                        'inventory.promocode.delete'
                    ])->plainTextToken,
            ])
            ->assertStatus(200)
            ->assertJsonStructure([
                'message'
            ]);
        $this->assertDatabaseHas('offers', [
            'status' => Offer::STATUS_ARCHIVED,
            'tenant_id' => $this->tenant->getKey(),
            'domain_id' => $this->tenant->primary_domain->getKey()
        ]);
    }

    public function test_if_list_of_all_promo_codes_are_Returned()
    {
        $this->withoutExceptionHandling();
        $promoCode = Offer::factory()->count(5)->create([
            'tenant_id' => $this->tenant->getKey(),
            'created_by' => $this->user->getKey(),
            'domain_id' => $this->tenant->primary_domain->getKey(),
            'title' => $this->faker->name,
            'offer_type' => OfferTypeEnum::PROMO_CODE
        ]);
        $response = $this
            ->actingAs($this->user, 'api')
            ->getJson('/api/v1/offers/', [
                'X-Tenant' => $this->tenant->getKey(),
                'Authorization' => 'Bearer ' . $this->user->createToken('test', [
                        'inventory.promocode.view'
                    ])->plainTextToken,
            ])
            ->assertSuccessful()
            ->assertJsonStructure([
                'data'
            ])->assertJsonCount(5, 'data');
    }

    public function test_if_selected_promo_code_Returned()
    {
        $this->withoutExceptionHandling();
        $title = $this->faker->name;
        $promoCode = Offer::factory()->create([
            'tenant_id' => $this->tenant->getKey(),
            'created_by' => $this->user->getKey(),
            'domain_id' => $this->tenant->primary_domain->getKey(),
            'title' => $title,
            'offer_type' => OfferTypeEnum::PROMO_CODE
        ]);
        $response = $this
            ->actingAs($this->user, 'api')
            ->getJson('/api/v1/offers/' . $promoCode->getKey(), [
                'X-Tenant' => $this->tenant->getKey(),
                'Authorization' => 'Bearer ' . $this->user->createToken('test', [
                        'inventory.promocode.view'
                    ])->plainTextToken,
            ])
            ->assertSuccessful()
            ->assertJsonStructure([
                'title'
            ]);
        $this->assertEquals($response->json()['title'], $title);
    }

    public function test_if_promo_code_can_be_created()
    {
        $this->withoutExceptionHandling();
        $this
            ->actingAs($this->user, 'api')
            ->postJson('/api/v1/offers', [
                'title' => 'Test Title',
                'offer_type' => OfferTypeEnum::PROMO_CODE,
                'code' => 'Test Code',
                'code_type' => collect(config('app.promocode.type'))->random(),
                'amount' => $this->faker->randomNumber(2),
                'status' => Offer::STATUS_ACTIVE,
                'products' => [$this->product->getKey() => 1]
            ], [
                'X-Tenant' => $this->tenant->getKey(),
                'Authorization' => 'Bearer ' . $this->user->createToken('test', [
                        'inventory.promocode.create'
                    ])->plainTextToken,
                'X-Subdomain' => $this->tenant->primary_domain->getKey()
            ])
            ->assertStatus(201)
            ->assertJsonStructure([
                'offer' => [
                    'title',
                    'code_type',
                ],
                'message'
            ]);
        $this->assertDatabaseHas('offers', [
            'title' => 'Test Title',
            'code' => 'Test Code',
            'tenant_id' => $this->tenant->getKey(),
            'domain_id' => $this->tenant->primary_domain->getKey()
        ]);
    }

    public function test_if_promo_code_can_be_updated()
    {
        $this->withoutExceptionHandling();
        $promoCode = Offer::factory()->create([
            'tenant_id' => $this->tenant->getKey(),
            'created_by' => $this->user->getKey(),
            'domain_id' => $this->tenant->primary_domain->getKey(),
            'title' => 'Test Title',
            'code' => 'Test Code',
            'offer_type' => OfferTypeEnum::PROMO_CODE
        ]);

        $this
            ->actingAs($this->user, 'api')
            ->putJson('/api/v1/offers/' . $promoCode->getKey(), [
                'title' => 'Test Title 1',
                'offer_type' => OfferTypeEnum::PROMO_CODE,
                'code' => 'Test Code updated',
                'code_type' => collect(config('app.promocode.type'))->random(),
                'amount' => $this->faker->randomNumber(2),
                'status' => Offer::STATUS_INACTIVE,
                'products' => [$this->product->getKey() => 1]
            ], [
                'X-Tenant' => $this->tenant->getKey(),
                'Authorization' => 'Bearer ' . $this->user->createToken('test', [
                        'inventory.promocode.update'
                    ])->plainTextToken,
                'X-Subdomain' => $this->tenant->primary_domain->getKey()
            ])->assertStatus(200)
            ->assertJsonStructure([
                'offer' => [
                    'title',
                    'code_type',
                ],
                'message'
            ]);
        $this->assertDatabaseHas('offers', [
            'title' => 'Test Title 1',
            'code' => 'Test Code updated',
            'tenant_id' => $this->tenant->getKey(),
            'domain_id' => $this->tenant->primary_domain->getKey()
        ]);
    }

    public function test_if_all_the_required_fields_are_empty()
    {
        $response = $this
            ->actingAs($this->user, 'api')
            ->postJson('/api/v1/offers', [
                'title' => null,
                'code' => null,
                'code_type' => null,
                'amount' => null,
                'offer_type' => '',
            ], [
                'X-Tenant' => $this->tenant->getKey(),
                'Authorization' => 'Bearer ' . $this->user->createToken('test', [
                        'inventory.promocode.create'
                    ])->plainTextToken,
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'title',
                'offer_type',
                'amount',
                'products'
            ]);
    }

    public function test_if_not_valid_start_date_submitted()
    {
        $promoCode = Offer::factory()->create([
            'tenant_id' => $this->tenant->getKey(),
            'created_by' => $this->user->getKey(),
            'domain_id' => $this->tenant->primary_domain->getKey(),
            'title' => 'Test Title',
            'offer_type' => OfferTypeEnum::PROMO_CODE,
            'code' => 'Test Code'
        ]);
        $payload = [
            'start_at' => Carbon::now()->endOfDay(),
            'expired_at' => Carbon::now()->endOfDay()->format('Y-m-d'),
            'min_order_price' => $this->faker->numberBetween($min = 10, $max = 100),
            'max_order_price' => $this->faker->numberBetween($min = 1000, $max = 100000),
            'apply_once_per_type' => collect(config('app.promocode.apply_once_per_type'))->random(),
            'max_redemption_time' => $this->faker->randomNumber(2),
            'step' => 3
        ];
        $this
            ->actingAs($this->user, 'api')
            ->putJson('/api/v1/offers/' . $promoCode->getKey(), $payload, [
                'X-Tenant' => $this->tenant->getKey(),
                'Authorization' => 'Bearer ' . $this->user->createToken('test', [
                        'inventory.promocode.update'
                    ])->plainTextToken,
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'start_at',
            ]);
    }

    public function test_if_expiry_date_before_than_start_date()
    {
        $promoCode = Offer::factory()->create([
            'tenant_id' => $this->tenant->getKey(),
            'created_by' => $this->user->getKey(),
            'domain_id' => $this->tenant->primary_domain->getKey(),
            'title' => 'Test Title',
            'offer_type' => OfferTypeEnum::PROMO_CODE,
            'code' => 'Test Code'
        ]);
        $payload = [
            'start_at' => Carbon::tomorrow()->format('Y-m-d'),
            'expired_at' => Carbon::yesterday()->format('Y-m-d'),
            'min_order_price' => $this->faker->numberBetween($min = 10, $max = 100),
            'max_order_price' => $this->faker->numberBetween($min = 1000, $max = 100000),
            'apply_once_per_type' => collect(config('app.promocode.apply_once_per_type'))->random(),
            'max_redemption_time' => $this->faker->randomNumber(2),
            'step' => 3
        ];
        $this
            ->actingAs($this->user, 'api')
            ->putJson('/api/v1/offers/' . $promoCode->getKey(), $payload, [
                'X-Tenant' => $this->tenant->getKey(),
                'Authorization' => 'Bearer ' . $this->user->createToken('test', [
                        'inventory.promocode.update'
                    ])->plainTextToken,
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'expired_at',
            ]);
    }

    public function test_if_min_and_max_order_price_validation()
    {
        $promoCode = Offer::factory()->create([
            'tenant_id' => $this->tenant->getKey(),
            'created_by' => $this->user->getKey(),
            'domain_id' => $this->tenant->primary_domain->getKey(),
            'title' => 'Test Title',
            'offer_type' => OfferTypeEnum::PROMO_CODE,
            'code' => 'Test Code'
        ]);
        $payload = [
            'start_at' => Carbon::now()->endOfDay()->format('Y-m-d'),
            'expired_at' => Carbon::now()->endOfDay()->format('Y-m-d'),
            'min_order_price' => $this->faker->numberBetween($min = 1000, $max = 10000),
            'max_order_price' => $this->faker->numberBetween($min = 10, $max = 100),
            'apply_once_per_type' => collect(config('app.promocode.apply_once_per_type'))->random(),
            'max_redemption_time' => $this->faker->randomNumber(2),
            'step' => 3
        ];
        $this
            ->actingAs($this->user, 'api')
            ->putJson('/api/v1/offers/' . $promoCode->getKey(), $payload, [
                'X-Tenant' => $this->tenant->getKey(),
                'Authorization' => 'Bearer ' . $this->user->createToken('test', [
                        'inventory.promocode.update'
                    ])->plainTextToken,
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'max_order_price',
            ]);
    }

    public function test_if_same_expiry_and_start_date_submitted()
    {
        $promoCode = Offer::factory()->create([
            'tenant_id' => $this->tenant->getKey(),
            'created_by' => $this->user->getKey(),
            'domain_id' => $this->tenant->primary_domain->getKey(),
            'title' => 'Test Title',
            'offer_type' => OfferTypeEnum::PROMO_CODE,
            'code' => 'Test Code'
        ]);
        $payload = [
            'start_at' => Carbon::now()->endOfDay()->format('Y-m-d'),
            'expired_at' => Carbon::now()->endOfDay()->format('Y-m-d'),
            'min_order_price' => $this->faker->numberBetween($min = 10, $max = 100),
            'max_order_price' => $this->faker->numberBetween($min = 1000, $max = 10000),
            'apply_once_per_type' => collect(config('app.promocode.apply_once_per_type'))->random(),
            'max_redemption_time' => $this->faker->randomNumber(2),
            'step' => 3
        ];
        $this
            ->actingAs($this->user, 'api')
            ->putJson('/api/v1/offers/' . $promoCode->getKey(), $payload, [
                'X-Tenant' => $this->tenant->getKey(),
                'Authorization' => 'Bearer ' . $this->user->createToken('test', [
                        'inventory.promocode.update'
                    ])->plainTextToken,
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'expired_at',
            ]);
    }

    public function test_if_apply_once_per_type_value_is_valid()
    {
        $promoCode = Offer::factory()->create([
            'tenant_id' => $this->tenant->getKey(),
            'created_by' => $this->user->getKey(),
            'domain_id' => $this->tenant->primary_domain->getKey(),
            'title' => 'Test Title',
            'offer_type' => OfferTypeEnum::PROMO_CODE,
            'code' => 'Test Code'
        ]);
        $payload = [
            'start_at' => Carbon::now()->endOfDay()->format('Y-m-d'),
            'expired_at' => Carbon::tomorrow()->endOfDay()->format('Y-m-d'),
            'min_order_price' => $this->faker->numberBetween($min = 10, $max = 100),
            'max_order_price' => $this->faker->numberBetween($min = 1000, $max = 10000),
            'apply_once_per_type' => $this->faker->name,
            'max_redemption_time' => $this->faker->randomNumber(2),
            'step' => 3
        ];
        $this
            ->actingAs($this->user, 'api')
            ->putJson('/api/v1/offers/' . $promoCode->getKey(), $payload, [
                'X-Tenant' => $this->tenant->getKey(),
                'Authorization' => 'Bearer ' . $this->user->createToken('test', [
                        'inventory.promocode.update'
                    ])->plainTextToken,
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'apply_once_per_type',
            ]);
    }

    /**
     * @dataProvider providePromocodeFilterData
     */
    public function test_if_promocode_works_with_filters($filterValues, $expectedCount)
    {
        $commonHeaders = [
            'tenant_id' => $this->tenant->getKey(),
            'created_by' => $this->user->getKey(),
            'domain_id' => $this->tenant->primary_domain->getKey(),
        ];

        Offer::factory()->count(2)->create($commonHeaders + [
                'title' => 'Test Title',
                'offer_type' => OfferTypeEnum::PROMO_CODE,
                'code' => 'ABC123',
                'amount' => 100,
            ]);

        Offer::factory()->count(10)->create($commonHeaders + [
                'title' => 'SOMETHING ELSE',
                'offer_type' => OfferTypeEnum::PROMO_CODE,
                'code' => 'XYZ',
                'amount' => 1000,
            ]);

        $filters = http_build_query([
            'filter' => $filterValues
        ]);

        $this
            ->getJson('/api/v1/offers?' . $filters, [
                'X-Tenant' => $this->tenant->getKey(),
                'X-Subdomain' => $this->domain->getKey(),
                'Authorization' => 'Bearer ' . $this->user->createToken('test', [
                        'inventory.promocode.view'
                    ])->plainTextToken,
            ])
            ->assertStatus(200)
            ->assertJsonCount($expectedCount, 'data');
    }

    public function providePromocodeFilterData()
    {
        return [
            [
                [
                    'title' => 'Test Title',
                ],
                2
            ], [
                [
                    'code' => 'XYZ',
                ],
                10
            ], [
                [
                    'title' => 'SOMETHING',
                ],
                10
            ], [
                [
                    'amount' => 100000,
                ],
                0
            ], [
                [
                    'title' => 'something that doesn\'t exists',
                ],
                0
            ],
        ];
    }

    /**
     * @dataProvider providePromocodeFilterSortData
     */
    public function test_if_query_builder_filters_could_allow_same_field_tested($filterValues, $expectedCount)
    {
        $commonHeaders = [
            'tenant_id' => $this->tenant->getKey(),
            'created_by' => $this->user->getKey(),
            'domain_id' => $this->tenant->primary_domain->getKey(),
        ];

        Offer::factory()->count(5)->create($commonHeaders + [
                'title' => 'Test Title',
                'offer_type' => OfferTypeEnum::PROMO_CODE,
                'code' => 'ABC123',
                'amount' => 100,
            ]);

        Offer::factory()->count(1)->create($commonHeaders + [
                'title' => 'SOMETHING ELSE ABC',
                'offer_type' => OfferTypeEnum::PROMO_CODE,
                'code' => 'XYZ',
                'amount' => 1000,
            ]);

        $response = QueryBuilder::for(Offer::class)->allowedFilters(['title', 'code'])->allowedSorts([
            'title', 'code', 'code_type', 'amount', 'status'
        ])->select('code', 'title', 'id', 'amount')->get()->toArray();

        $filters = http_build_query([
            'filter' => $filterValues
        ]);

        $this
            ->getJson('/api/v1/offers?' . $filters, [
                'X-Tenant' => $this->tenant->getKey(),
                'X-Subdomain' => $this->domain->getKey(),
                'Authorization' => 'Bearer ' . $this->user->createToken('test', [
                        'inventory.promocode.view'
                    ])->plainTextToken,
            ])
            ->assertStatus(200)
            ->assertJsonCount($expectedCount, 'data');
    }

    public function providePromocodeFilterSortData()
    {
        return [
            [
                [
                    'title' => 'Test Title',
                ],
                5
            ], [
                [
                    'code' => 'XYZ',
                ],
                1
            ], [
                [
                    'title' => 'SOMETHING',
                ],
                1
            ], [
                [
                    'amount' => 100000,
                ],
                0
            ], [
                [
                    'title' => 'something that doesn\'t exists',
                ],
                0
            ],
        ];
    }

    /**
     * @dataProvider providePromocodeSortingData
     */
    public function test_if_promocode_works_with_sorting($sortingValues, $expectedFirstTitle)
    {
        $commonHeaders = [
            'tenant_id' => $this->tenant->getKey(),
            'created_by' => $this->user->getKey(),
            'domain_id' => $this->tenant->primary_domain->getKey(),
        ];
        Offer::factory()->count(1)->create($commonHeaders + [
                'title' => 'C listing',
                'offer_type' => OfferTypeEnum::PROMO_CODE,
                'code' => 'C',
                'amount' => 10000,
            ]);
        Offer::factory()->count(1)->create($commonHeaders + [
                'title' => 'A listing',
                'offer_type' => OfferTypeEnum::PROMO_CODE,
                'code' => 'A',
                'amount' => 200,
            ]);
        Offer::factory()->count(1)->create($commonHeaders + [
                'title' => 'B listing',
                'code' => 'B',
                'offer_type' => OfferTypeEnum::PROMO_CODE,
                'amount' => 1000,
            ]);
        $filters = http_build_query([
            'sort' => $sortingValues
        ]);
        $response = $this
            ->getJson('/api/v1/offers?' . $filters, [
                'X-Tenant' => $this->tenant->getKey(),
                'X-Subdomain' => $this->domain->getKey(),
                'Authorization' => 'Bearer ' . $this->user->createToken('test', [
                        'inventory.promocode.view'
                    ])->plainTextToken,
            ])
            ->assertStatus(200);
        $this->assertEquals($expectedFirstTitle, $response->json('data.0.title'));
    }

    public function providePromocodeSortingData()
    {
        return [
            [
                [
                    'title'
                ],
                'A listing',
            ],
            [
                [
                    '-title'
                ],
                'C listing',
            ],
            [
                [
                    '-amount'
                ],
                'C listing',
            ],
            [
                [
                    'amount'
                ],
                'A listing',
            ],
        ];
    }
}
