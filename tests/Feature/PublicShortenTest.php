<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicShortenTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_receives_shortened_link_in_inertia_props(): void
    {
        $response = $this->post('/shorten', [
            'original_url' => 'https://example.com/some/long/path',
            'expires_in'   => '14d',
            'is_burn'      => false,
        ]);

        $response->assertRedirect();

        $home = $this->followingRedirects()->get('/');
        $home->assertOk();

        $props = $home->viewData('page')['props'];
        $this->assertNotNull($props['flash']['shortened'] ?? null, 'flash.shortened missing from Inertia props');
        $this->assertSame('https://example.com/some/long/path', $props['flash']['shortened']['original_url']);
    }
}
