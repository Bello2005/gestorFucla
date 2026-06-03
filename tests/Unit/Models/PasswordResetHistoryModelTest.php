<?php

namespace Tests\Unit\Models;

use App\Models\PasswordResetHistory;
use Tests\TestCase;

class PasswordResetHistoryModelTest extends TestCase
{
    // ── createReset ───────────────────────────────────────────────────────

    public function test_create_reset_persists_correct_fields(): void
    {
        $user   = $this->createUser();
        $record = PasswordResetHistory::createReset($user->id, 'email', 'Password forgotten', 'token-abc');

        $this->assertSame($user->id, $record->user_id);
        $this->assertSame('email', $record->type);
        $this->assertSame('Password forgotten', $record->reason);
        $this->assertSame('token-abc', $record->token);
        $this->assertFalse($record->completed);
        $this->assertNull($record->completed_at);
    }

    public function test_create_reset_with_null_reason_and_token(): void
    {
        $user   = $this->createUser();
        $record = PasswordResetHistory::createReset($user->id, 'temporal');

        $this->assertNull($record->reason);
        $this->assertNull($record->token);
        $this->assertFalse($record->completed);
    }

    // ── markAsCompleted ───────────────────────────────────────────────────

    public function test_mark_as_completed_sets_completed_true(): void
    {
        $user   = $this->createUser();
        $record = PasswordResetHistory::createReset($user->id, 'email');

        $record->markAsCompleted();

        $this->assertTrue($record->completed);
    }

    public function test_mark_as_completed_sets_completed_at_timestamp(): void
    {
        $user   = $this->createUser();
        $record = PasswordResetHistory::createReset($user->id, 'email');

        $record->markAsCompleted();

        $this->assertNotNull($record->completed_at);
        $this->assertTrue($record->completed_at->isToday());
    }

    public function test_mark_as_completed_persists_to_database(): void
    {
        $user   = $this->createUser();
        $record = PasswordResetHistory::createReset($user->id, 'email');
        $record->markAsCompleted();

        $this->assertDatabaseHas('password_reset_history', [
            'id'        => $record->id,
            'completed' => true,
        ]);
    }
}
