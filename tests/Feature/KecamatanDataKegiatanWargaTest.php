<?php

namespace Tests\Feature;

use App\Domains\Wilayah\DataKegiatanWarga\Models\DataKegiatanWarga;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class KecamatanDataKegiatanWargaTest extends TestCase
{
    use RefreshDatabase;

    private const ACTIVE_BUDGET_YEAR = 2026;

    protected Area $kecamatan;

    protected Area $desa1;

    protected Area $desa2;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'kecamatan-sekretaris']);
        Role::firstOrCreate(['name' => 'desa-sekretaris']);

        $this->kecamatan = Area::create([
            'name' => 'Pecalungan',
            'level' => 'kecamatan',
            'code' => '33.25.14',
        ]);

        $this->desa1 = Area::create([
            'name' => 'Gombong',
            'level' => 'desa',
            'parent_id' => $this->kecamatan->id,
            'code' => '33.25.14.2001',
        ]);

        $this->desa2 = Area::create([
            'name' => 'Bandung',
            'level' => 'desa',
            'parent_id' => $this->kecamatan->id,
            'code' => '33.25.14.2002',
        ]);
    }

    #[Test]
    public function admin_kecamatan_dapat_melihat_rekap_data_kegiatan_warga_per_desa(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatan->id,
            'scope' => 'kecamatan',
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $adminKecamatan->assignRole('kecamatan-sekretaris');

        $adminDesa1 = User::factory()->create(['area_id' => $this->desa1->id]);

        DataKegiatanWarga::create([
            'kegiatan' => 'Kerja Bakti',
            'aktivitas' => true,
            'keterangan' => 'Bersih saluran',
            'level' => 'desa',
            'area_id' => $this->desa1->id,
            'created_by' => $adminDesa1->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $response = $this->actingAs($adminKecamatan)->get('/kecamatan/data-kegiatan-warga');

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Kecamatan/DataKegiatanWarga/Index')
                ->has('recapItems', 2)
                ->where('recapItems.0.nama_desa', 'Gombong')
                ->where('recapItems.0.activities.1.kegiatan', 'Kerja Bakti')
                ->where('recapItems.0.activities.1.aktivitas', true)
                ->where('recapItems.1.nama_desa', 'Bandung')
                ->where('recapItems.1.activities.1.aktivitas', false);
        });
    }

    #[Test]
    public function rekap_kecamatan_mendukung_zero_fill_untuk_desa_tanpa_data(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatan->id,
            'scope' => 'kecamatan',
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $adminKecamatan->assignRole('kecamatan-sekretaris');

        $response = $this->actingAs($adminKecamatan)->get('/kecamatan/data-kegiatan-warga');

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Kecamatan/DataKegiatanWarga/Index')
                ->has('recapItems', 2)
                ->where('recapItems.0.activities.0.aktivitas', false)
                ->where('recapItems.1.activities.0.aktivitas', false);
        });
    }

    #[Test]
    public function rekap_mengikuti_urutan_kode_wilayah(): void
    {
        // Ubah kode agar Bandung (2002) datang sebelum Gombong (2003)
        $this->desa1->update(['code' => '33.25.14.2003']);
        $this->desa2->update(['code' => '33.25.14.2002']);

        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatan->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('kecamatan-sekretaris');

        $response = $this->actingAs($adminKecamatan)->get('/kecamatan/data-kegiatan-warga');

        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->where('recapItems.0.nama_desa', 'Bandung')
                ->where('recapItems.1.nama_desa', 'Gombong');
        });
    }

    #[Test]
    public function pengguna_non_admin_kecamatan_tidak_bisa_mengakses_modul_data_kegiatan_warga_kecamatan(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desa1->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('desa-sekretaris');

        $response = $this->actingAs($adminDesa)->get('/kecamatan/data-kegiatan-warga');

        $response->assertStatus(403);
    }
}
