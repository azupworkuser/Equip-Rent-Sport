<?php

namespace Tests\Feature\API\V1;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use DatabaseMigrations;

    public function test_if_verification_email_can_be_resent()
    {
        Notification::fake();
        $this
            ->actingAs($this->user)
            ->postJson('/api/v1/email/verify/resend', [
                'email' => $this->user->email,
            ])
            ->assertStatus(202);

        Notification::assertSentTo($this->user, VerifyEmail::class);
    }
}
