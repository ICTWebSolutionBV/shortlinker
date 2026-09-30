<?php

namespace Tests\Feature;

use App\Models\ShortLink;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShortLinkExpiryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['auth.two_factor_enabled' => false]);
    }

    public function test_custom_expiry_is_read_in_the_users_timezone(): void
    {
        $user = User::factory()->create(['timezone' => 'Europe/Amsterdam']);

        $this->actingAs($user)->post('/links', [
            'original_url' => 'https://example.com',
            'expires_in'   => 'custom',
            'expires_at'   => '2099-07-01T12:00',
        ])->assertSessionHasNoErrors()->assertRedirect();

        // 12:00 CEST is 10:00 UTC.
        $this->assertSame('2099-07-01 10:00:00', ShortLink::firstOrFail()->expires_at->utc()->toDateTimeString());
    }

    public function test_custom_expiry_without_a_date_is_rejected(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/links', [
            'original_url' => 'https://example.com',
            'expires_in'   => 'custom',
            'expires_at'   => null,
        ])->assertSessionHasErrors('expires_at');

        $this->assertSame(0, ShortLink::count());
    }

    public function test_custom_expiry_in_the_past_is_rejected(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/links', [
            'original_url' => 'https://example.com',
            'expires_in'   => 'custom',
            'expires_at'   => '2020-01-01T12:00',
        ])->assertSessionHasErrors('expires_at');
    }

    public function test_link_can_be_updated_with_a_custom_expiry_date(): void
    {
        $user = User::factory()->create(['timezone' => 'UTC']);
        $link = ShortLink::create([
            'user_id'      => $user->id,
            'alias'        => 'abcde',
            'original_url' => 'https://example.com',
            'expires_at'   => now()->addDays(14),
        ]);
        $expiresAt = now()->addMonth()->startOfMinute();

        $this->actingAs($user)->put("/links/{$link->id}", [
            'original_url' => 'https://example.com',
            'alias'        => 'abcde',
            'is_active'    => true,
            'is_burn'      => false,
            'is_tracking'  => true,
            'expires_in'   => 'custom',
            'expires_at'   => $expiresAt->format('Y-m-d\TH:i'),
        ])->assertSessionHasNoErrors()->assertRedirect();

        $this->assertTrue($link->fresh()->expires_at->equalTo($expiresAt));
    }
}
