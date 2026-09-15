<?php
declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class NotFoundTest extends TestCase
{
    private const MISSING_URL = '/page-that-does-not-exist';

    public function test_missing_page_returns_404(): void
    {
        $this->get(self::MISSING_URL)->assertStatus(404);
    }

    public function test_missing_page_shows_custom_error_view(): void
    {
        $response = $this->get(self::MISSING_URL);
        $response->assertStatus(404);
        $response->assertViewIs('errors.404');
    }

    public function test_custom_error_page_contains_expected_text(): void
    {
        $response = $this->get(self::MISSING_URL);
        $response->assertStatus(404);
        $response->assertSee('Страница не найдена');
    }
}