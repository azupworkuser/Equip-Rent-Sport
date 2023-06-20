<?php

namespace App\Console\Commands;

use App\Models\Offer;
use App\Models\States\Offer\OfferStates;
use App\CoreLogic\Services\OfferService;
use Illuminate\Console\Command;

class UpdateOfferExpireStatus extends Command
{
    public function __construct(
        public OfferService $offerService,
    ) {
        parent::__construct();
        $this->offerService = $offerService;
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'booking:promocode:expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'It will update the promo code status to expiry if the expiration date completed';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->offerService->getPromoCodesForExpiry()->each(function (Offer $offer) {
            $offer->status->transitionTo(OfferStates::make(Offer::STATUS_EXPIRED, $offer));
        });
        return Command::SUCCESS;
    }
}
