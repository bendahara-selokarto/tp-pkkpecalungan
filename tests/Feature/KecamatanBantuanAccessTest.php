<?php

namespace Tests\Feature;

use App\Domains\Wilayah\Bantuan\Models\Bantuan;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class KecamatanBantuanAccessTest extends TestCase
{
    use RefreshDatabase;

    private const ACTIVE_BUDGET_YEAR = 2026;

    #[Test]
    public function kecamatan_sekretaris_dapat_membuka_menu_bantuan(): void
    {
        Role::firstOrCreate(['name' => 'kecamatan-sekretaris']);

        $kecamatan = Area::create([
            'name' => 'Pecalungan',
            'level' => 'kecamatan',
        ]);

        $user = User::factory()->create([
            'area_id' => $kecamatan->id,
            'scope' => 'kecamatan',
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $user->assignRole('kecamatan-sekretaris');

        Bantuan::create([
            'name' => 'Bantuan Kecamatan',
            'category' => 'uang',
            'description' => null,
            'source' => 'kabupaten',
            'amount' => 1000000,
            'received_date' => '2026-02-11',
            'level' => 'kecamatan',
            'area_id' => $kecamatan->id,
            'created_by' => $user->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
            'group' => 'sekretaris-tpk',
        ]);

        $response = $this->actingAs($user)->get('/kecamatan/bantuans');

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Kecamatan/Bantuan/Index')
                ->has('bantuans.data', 1)
                ->where('bantuans.data.0.lokasi_penerima', 'Bantuan Kecamatan');
        });
    }
}
