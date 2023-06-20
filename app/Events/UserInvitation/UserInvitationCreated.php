<?php

namespace App\Events\UserInvitation;

use App\Models\UserInvitation;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserInvitationCreated
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Model instance.
     *
     * @var UserInvitation
     */
    public UserInvitation $userInvitation;

    /**
     * Create a new event instance.
     *
     * @param  UserInvitation  $userInvitation
     * @return void
     */
    public function __construct(UserInvitation $userInvitation)
    {
        $this->userInvitation = $userInvitation;
    }

    /**
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
