<?php

namespace Tests\Unit\Models;

use App\Models\EmailChangeRequest;
use Tests\TestCase;

class EmailChangeRequestModelTest extends TestCase
{
    // ── isExpired ─────────────────────────────────────────────────────────

    public function test_is_not_expired_when_expires_in_future(): void
    {
        $user = $this->createUser();
        $req  = EmailChangeRequest::create([
            'user_id'       => $user->id,
            'current_email' => 'old@test.com',
            'new_email'     => 'new@test.com',
            'token'         => 'token-future',
            'expires_at'    => now()->addHour(),
        ]);

        $this->assertFalse($req->isExpired());
    }

    public function test_is_expired_when_expires_in_past(): void
    {
        $user = $this->createUser();
        $req  = EmailChangeRequest::create([
            'user_id'       => $user->id,
            'current_email' => 'old@test.com',
            'new_email'     => 'new@test.com',
            'token'         => 'token-past',
            'expires_at'    => now()->subHour(),
        ]);

        $this->assertTrue($req->isExpired());
    }

    // ── isVerified ────────────────────────────────────────────────────────

    public function test_is_not_verified_when_verified_at_is_null(): void
    {
        $req = new EmailChangeRequest(['verified_at' => null]);
        $this->assertFalse($req->isVerified());
    }

    public function test_is_verified_when_verified_at_is_set(): void
    {
        $req = new EmailChangeRequest(['verified_at' => now()]);
        $this->assertTrue($req->isVerified());
    }

    // ── createRequest ─────────────────────────────────────────────────────

    public function test_create_request_persists_correct_fields(): void
    {
        $user = $this->createUser();
        $req  = EmailChangeRequest::createRequest($user->id, 'old@test.com', 'new@test.com');

        $this->assertSame($user->id, $req->user_id);
        $this->assertSame('old@test.com', $req->current_email);
        $this->assertSame('new@test.com', $req->new_email);
        $this->assertSame(64, strlen($req->token));
        $this->assertTrue($req->expires_at->isFuture());
    }

    public function test_create_request_invalidates_previous_pending_requests(): void
    {
        $user = $this->createUser();

        EmailChangeRequest::createRequest($user->id, 'old@test.com', 'first@test.com');
        EmailChangeRequest::createRequest($user->id, 'old@test.com', 'second@test.com');

        $remaining = EmailChangeRequest::where('user_id', $user->id)->get();

        $this->assertCount(1, $remaining);
        $this->assertSame('second@test.com', $remaining->first()->new_email);
    }
}
