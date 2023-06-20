<?php

namespace App\Jobs;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class CreateS3BucketJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public Tenant $tenant
    ) {
    }

    /**
     * @return void
     * @throws \JsonException
     */
    public function handle(): void
    {
        if (app()->environment() === 'testing' || app()->environment() === 'local') {
            return;
        }

        $this
            ->tenant
            ->run(fn () => $this->tenant->bucket()->create());
    }
}
