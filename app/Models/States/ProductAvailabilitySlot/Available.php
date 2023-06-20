<?php

namespace App\Models\States\ProductAvailabilitySlot;

class Available extends ProductAvailabilitySlotState
{
    public function hold()
    {
        $this
            ->getModel()
            ->status
            ->transitionTo(Hold::class);
    }
}
