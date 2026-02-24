<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Mockery;
use Tests\TestCase;

class UiRuntimeErrorLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_endpoint_runtime_error_memerlukan_authentication(): void
    {
        $response = $this->post(route('ui.runtime-errors.store'), [
            'message' => 'Runtime test error',
            'source' => 'window.error',
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_endpoint_runtime_error_menyimpan_log_ringan_dengan_payload_valid(): void
    {
        Log::shouldReceive('warning')
            ->once()
            ->with('ui.runtime_error', Mockery::on(function (array $context): bool {
                return ($context['source'] ?? null) === 'window.error'
                    && ($context['message'] ?? null) === 'Runtime test error';
            }));

        $user = User::factory()->create();
        $response = $this->actingAs($user)->post(route('ui.runtime-errors.store'), [
            'message' => 'Runtime test error',
            'source' => 'window.error',
            'url' => 'http://localhost/dashboard',
        ]);

        $response->assertOk()->assertJson(['ok' => true]);
    }
}
