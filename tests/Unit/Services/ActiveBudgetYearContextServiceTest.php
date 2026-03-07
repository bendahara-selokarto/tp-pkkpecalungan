<?php

namespace Tests\Unit\Services;

use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActiveBudgetYearContextServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_mengembalikan_tahun_anggaran_aktif_dari_user(): void
    {
        $user = User::factory()->create([
            'active_budget_year' => 2028,
        ]);

        $service = app(ActiveBudgetYearContextService::class);

        $this->assertSame(2028, $service->resolveForUser($user));
    }

    public function test_service_fallback_ke_tahun_sistem_jika_user_belum_memiliki_tahun_aktif(): void
    {
        $user = User::factory()->create([
            'active_budget_year' => null,
        ]);

        $service = app(ActiveBudgetYearContextService::class);

        $this->assertSame((int) now()->format('Y'), $service->resolveForUser($user));
    }
}
