<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AvailabilitySessionResource;
use App\Http\Resources\ProductOptionAvailabilityEventResource;
use App\Models\ProductOption;
use App\Models\ProductOptionAvailabilityEvent;
use App\CoreLogic\Services\AvailabilitySessionService;
use App\CoreLogic\Services\ProductOptionAvailabilityEventService;
use Illuminate\Http\Request;

class AvailabilitySessionController extends Controller
{
    /**
     * @param AvailabilitySessionService $sessionAvailabilityService
     * @param ProductOptionAvailabilityEventService $sessionAvailabilityEventService
     */
    public function __construct(
        public AvailabilitySessionService $sessionAvailabilityService,
        public ProductOptionAvailabilityEventService $sessionAvailabilityEventService
    ) {
    }

    /**
     * @param Request $request
     * @param ProductOption $productOption
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request, ProductOption $productOption)
    {
        $request->validate([
            'date' => 'required|date'
        ]);
        $availabilities = [];

        $sessions = $this
            ->sessionAvailabilityService
            ->getAvailableSessions(
                $productOption,
                $request->input('date')
            );

        $availabilityEvents = $this
            ->sessionAvailabilityEventService
            ->find($sessions->pluck('product_option_availability_event_id')->toArray(), ['productOption', 'pricings'])
            ->map(function (ProductOptionAvailabilityEvent $event) use ($sessions) {
                $event->sessions = AvailabilitySessionResource::collection(
                    $sessions->where('product_option_availability_event_id', $event->getKey())
                );

                return $event;
            });

        return response()->json(
            ProductOptionAvailabilityEventResource::collection(
                $availabilityEvents
            )
        );
    }
}
