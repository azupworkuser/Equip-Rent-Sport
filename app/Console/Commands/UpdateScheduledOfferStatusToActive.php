<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Offer;
use App\CoreLogic\Services\OfferService;
use App\Models\States\Offer\OfferStates;

class UpdateScheduledOfferStatusToActive extends Command
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
    protected $signature = 'booking:promocode:makeActive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "It will update promo codes that are in 'scheduled' status to 'active' when the start date reaches";


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->offerService->getScheduledPromoCodes()->each(function (Offer $offer) {
            $offer->status->transitionTo(OfferStates::make(Offer::STATUS_ACTIVE, $offer));
        });
        return Command::SUCCESS;
    }
}
