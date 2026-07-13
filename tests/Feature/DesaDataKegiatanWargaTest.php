<?php

namespace Tests\Feature;

use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\DataKegiatanWarga\Models\DataKegiatanWarga;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DesaDataKegiatanWargaTest extends TestCase
{
    use RefreshDatabase;

    private const ACTIVE_BUDGET_YEAR = 2026;

    protected Area $kecamatan;

    protected Area $desaA;

    protected Area $desaB;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'desa-pokja-i']);
        Role::firstOrCreate(['name' => 'kecamatan-pokja-i']);

        $this->kecamatan = Area::create([
            'name' => 'Pecalungan',
            'level' => 'kecamatan',
        ]);

        $this->desaA = Area::create([
            'name' => 'Gombong',
            'level' => 'desa',
            'parent_id' => $this->kecamatan->id,
        ]);

        $this->desaB = Area::create([
            'name' => 'Bandung',
            'level' => 'desa',
            'parent_id' => $this->kecamatan->id,
        ]);
    }

    #[Test]
    public function admin_desa_dapat_melihat_daftar_data_kegiatan_warga_di_desanya_sendiri(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('desa-pokja-i');

        DataKegiatanWarga::create([
            'kegiatan' => 'Penghayatan dan Pengamalan Pancasila',
            'aktivitas' => true,
            'keterangan' => 'Pembinaan rutin RT 01',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
        ]);

        DataKegiatanWarga::create([
            'kegiatan' => 'Kerja Bakti',
            'aktivitas' => true,
            'keterangan' => 'Bersih lingkungan',
            'level' => 'desa',
            'area_id' => $this->desaB->id,
            'created_by' => $adminDesa->id,
        ]);

        $response = $this->actingAs($adminDesa)->get('/desa/data-kegiatan-warga');

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Desa/DataKegiatanWarga/Index')
                ->has('dataKegiatanWargaItems.data', 1)
                ->where('dataKegiatanWargaItems.data.0.kegiatan', 'Penghayatan dan Pengamalan Pancasila')
                ->where('dataKegiatanWargaItems.data.0.aktivitas_label', 'Ya')
                ->where('dataKegiatanWargaItems.total', 1)
                ->where('filters.per_page', 10);
        });
    }

    #[Test]
    public function admin_desa_dapat_menambah_data_kegiatan_warga_dengan_relasi_sumber(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $adminDesa->assignRole('desa-pokja-i');

        $activity = Activity::create([
            'title' => 'Log Kerja Bakti',
            'activity_date' => '2026-06-11',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
            'group' => 'pokja-i',
        ]);

        $this->actingAs($adminDesa)->post('/desa/data-kegiatan-warga', [
            'kegiatan' => 'Kerja Bakti',
            'aktivitas' => true,
            'keterangan' => 'Mengikuti log #'.$activity->id,
            'source_module' => 'Activity',
            'source_id' => $activity->id,
        ])->assertStatus(302);

        $this->assertDatabaseHas('data_kegiatan_wargas', [
            'kegiatan' => 'Kerja Bakti',
            'source_module' => 'Activity',
            'source_id' => $activity->id,
            'area_id' => $this->desaA->id,
        ]);
    }

    #[Test]
    public function admin_desa_dapat_memperbarui_dan_menghapus_data_kegiatan_warga(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $adminDesa->assignRole('desa-pokja-i');

        $dataKegiatanWarga = DataKegiatanWarga::create([
            'kegiatan' => 'Rukun Kematian',
            'aktivitas' => true,
            'keterangan' => 'Takziyah warga',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
        ]);

        $this->actingAs($adminDesa)->put(route('desa.data-kegiatan-warga.update', $dataKegiatanWarga->id), [
            'kegiatan' => 'Rukun Kematian',
            'aktivitas' => false,
            'keterangan' => 'Tidak ada kegiatan bulan ini',
            'source_module' => null,
            'source_id' => null,
        ])->assertStatus(302);

        $this->assertDatabaseHas('data_kegiatan_wargas', [
            'id' => $dataKegiatanWarga->id,
            'aktivitas' => false,
            'keterangan' => 'Tidak ada kegiatan bulan ini',
        ]);

        $this->actingAs($adminDesa)->delete(route('desa.data-kegiatan-warga.destroy', $dataKegiatanWarga->id))
            ->assertStatus(302);

        $this->assertDatabaseMissing('data_kegiatan_wargas', ['id' => $dataKegiatanWarga->id]);
    }

    #[Test]
    public function pengguna_non_admin_desa_tidak_bisa_mengakses_modul_data_kegiatan_warga_desa(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatan->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('kecamatan-pokja-i');

        $response = $this->actingAs($adminKecamatan)->get('/desa/data-kegiatan-warga');

        $response->assertStatus(403);
    }
}
