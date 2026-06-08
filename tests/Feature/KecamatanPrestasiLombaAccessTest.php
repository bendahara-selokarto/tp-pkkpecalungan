<?php

namespace Tests\Feature;

use App\Domains\Wilayah\Models\Area;
use App\Domains\Wilayah\PrestasiLomba\Models\PrestasiLomba;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class KecamatanPrestasiLombaAccessTest extends TestCase
{
    use RefreshDatabase;

    private const ACTIVE_BUDGET_YEAR = 2026;

    #[Test]
    public function kecamatan_sekretaris_dapat_membuka_menu_prestasi_lomba(): void
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

        PrestasiLomba::create([
            'tahun' => self::ACTIVE_BUDGET_YEAR,
            'jenis_lomba' => 'Lomba PKK Kecamatan',
            'lokasi' => 'Pendopo Kecamatan',
            'prestasi_kecamatan' => true,
            'prestasi_kabupaten' => false,
            'prestasi_provinsi' => false,
            'prestasi_nasional' => false,
            'keterangan' => null,
            'level' => 'kecamatan',
            'area_id' => $kecamatan->id,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get('/kecamatan/prestasi-lomba');

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Kecamatan/PrestasiLomba/Index')
                ->has('prestasiLombaItems.data', 1)
                ->where('prestasiLombaItems.data.0.jenis_lomba', 'Lomba PKK Kecamatan');
        });
    }
}
