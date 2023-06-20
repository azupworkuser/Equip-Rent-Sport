<?php

namespace App\Events\Domain;

use App\Models\Domain;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DomainCreated
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

     /**
     * Model instance.
     *
     * @var Domain
     */
    public Domain $domain;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Domain $domain)
    {
        $this->domain = $domain;
    }
}
