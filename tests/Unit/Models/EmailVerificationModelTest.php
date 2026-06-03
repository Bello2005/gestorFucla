<?php

namespace Tests\Unit\Models;

use App\Models\EmailVerification;
use Tests\TestCase;

class EmailVerificationModelTest extends TestCase
{
    // ── isExpired ─────────────────────────────────────────────────────────

    public function test_is_not_expired_when_future(): void
    {
        $user = $this->createUser();
        $v    = EmailVerification::create([
            'user_id'       => $user->id,
            'current_email' => 'a@test.com',
            'new_email'     => 'b@test.com',
            'token'         => 'token-ok',
            'expires_at'    => now()->addHour(),
        ]);

        $this->assertFalse($v->isExpired());
    }

    public function test_is_expired_when_past(): void
    {
        $user = $this->createUser();
        $v    = EmailVerification::create([
            'user_id'       => $user->id,
            'current_email' => 'a@test.com',
            'new_email'     => 'b@test.com',
            'token'         => 'token-old',
            'expires_at'    => now()->subHour(),
        ]);

        $this->assertTrue($v->isExpired());
    }

    // ── createVerification ────────────────────────────────────────────────

    public function test_create_verification_persists_correct_fields(): void
    {
        $user = $this->createUser();
        $v    = EmailVerification::createVerification($user->id, 'old@test.com', 'new@test.com');

        $this->assertSame($user->id, $v->user_id);
        $this->assertSame('old@test.com', $v->current_email);
        $this->assertSame('new@test.com', $v->new_email);
        $this->assertSame(64, strlen($v->token));
        $this->assertTrue($v->expires_at->isFuture());
        $this->assertEmpty($v->verified);
    }

    public function test_create_verification_deletes_previous_pending(): void
    {
        $user = $this->createUser();

        EmailVerification::createVerification($user->id, 'old@test.com', 'first@test.com');
        EmailVerification::createVerification($user->id, 'old@test.com', 'second@test.com');

        $count = EmailVerification::where('user_id', $user->id)->where('verified', false)->count();
        $this->assertSame(1, $count);
    }
}
