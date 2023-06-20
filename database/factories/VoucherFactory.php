<?php

namespace Database\Factories;

use App\Models\Offer;
use App\Models\States\Offer\Active;
use App\Models\Tenant;
use App\Models\User;
use App\CoreLogic\Enum\Offer\OfferTypeEnum;
use App\CoreLogic\Enum\Offer\VoucherTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

class VoucherFactory extends Factory
{
    protected $model = Offer::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $payload = [
            'title' => $this->faker->word,
            'code' => $this->faker->name,
            'code_type' => 'fixed',
            'amount' => $this->faker->randomNumber(2),
            'status' => Active::class,
            'tenant_id' => fn() => Tenant::factory()->create()->getKey(),
            'created_by' => fn() => User::factory()->create()->getKey(),
            'domain_id' => 1,
            'offer_type' => OfferTypeEnum::VOUCHER
        ];
        return $payload;
    }
}
