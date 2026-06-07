<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_locale_can_be_switched_to_english(): void
    {
        $response = $this
            ->withSession(['locale' => 'ar'])
            ->get('/locale/en');

        $response->assertRedirect();
        $response->assertSessionHas('locale', 'en');
    }

    public function test_invalid_locale_returns_not_found(): void
    {
        $this->get('/locale/fr')->assertNotFound();
    }

    public function test_english_locale_is_applied_to_pages(): void
    {
        $response = $this
            ->withSession(['locale' => 'en'])
            ->get('/login');

        $response->assertOk();
        $response->assertSee('Log in', false);
    }
}
