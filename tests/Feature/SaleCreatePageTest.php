<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaleCreatePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_sales_create_page_renders_without_placeholder_text(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/sales/create');

        $response->assertOk();
        $response->assertSee('Add Items');
        $response->assertDontSeeText('hello@example.com');
    }
}
