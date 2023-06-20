<?php

namespace App\Models\States\ProductAvailabilitySlot;

class Hold extends ProductAvailabilitySlotState
{
    public function release()
    {
        $this->getModel()
            ->status
            ->transitionTo(Available::class);
    }
}
