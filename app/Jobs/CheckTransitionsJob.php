<?php

namespace App\Jobs;

use App\Models\ProductAvailabilitySlot;
use App\Models\States\ProductAvailabilitySlot\Hold;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\ModelStates\Events\StateChanged;

class CheckTransitionsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public string $initialState;
    public string $currentState;
    public Model $model;

    public function __construct(
        public StateChanged $event,
    ) {
        $this->initialState = $event->initialState;
        $this->currentState = $event->finalState;
        $this->model = $event->model;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
    }
}
