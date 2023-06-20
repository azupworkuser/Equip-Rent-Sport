<?php

namespace App\Events;

use App\Models\Deductible;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeductibleUpdated
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Model instance.
     *
     * @var Deductible
     */
    public Deductible $deductible;

    /**
     * Create a new event instance.
     *
     * @param Deductible $deductible
     * @return void
     */
    public function __construct(Deductible $deductible)
    {
        $this->deductible = $deductible;
    }
}
