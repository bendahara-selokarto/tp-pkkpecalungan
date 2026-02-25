<?php

namespace Tests\Feature;

use App\Domains\Wilayah\Bkr\Models\Bkr;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DesaBkrTest extends TestCase
{
    use RefreshDatabase;

    protected Area $kecamatan;
    protected Area $desaA;
    protected Area $desaB;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'admin-desa']);
        Role::create(['name' => 'admin-kecamatan']);

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
    public function admin_desa_dapat_melihat_daftar_bkr_di_desanya_sendiri(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        Bkr::create([
            'desa' => 'Gombong',
            'nama_bkr' => 'BKR Mawar',
            'no_tgl_sk' => '01/SK/BKR/2026',
            'nama_ketua_kelompok' => 'Siti Aminah',
            'jumlah_anggota' => 25,
            'kegiatan' => 'Pertemuan bulanan',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
        ]);

        Bkr::create([
            'desa' => 'Bandung',
            'nama_bkr' => 'BKR Melati',
            'no_tgl_sk' => '02/SK/BKR/2026',
            'nama_ketua_kelompok' => 'Rina Wati',
            'jumlah_anggota' => 30,
            'kegiatan' => 'Kelas pembinaan keluarga',
            'level' => 'desa',
            'area_id' => $this->desaB->id,
            'created_by' => $adminDesa->id,
        ]);

        $response = $this->actingAs($adminDesa)->get('/desa/bkr');

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Desa/Bkr/Index')
                ->has('bkrItems.data', 1)
                ->where('bkrItems.data.0.nama_bkr', 'BKR Mawar')
                ->where('bkrItems.total', 1)
                ->where('filters.per_page', 10);
        });
    }

    #[Test]
    public function daftar_bkr_desa_mendukung_pagination_dan_tetap_scoped(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        for ($index = 1; $index <= 12; $index++) {
            Bkr::create([
                'desa' => 'Gombong',
                'nama_bkr' => 'BKR Desa ' . $index,
                'no_tgl_sk' => '01/SK/BKR/2026',
                'nama_ketua_kelompok' => 'Ketua ' . $index,
                'jumlah_anggota' => 20 + $index,
                'kegiatan' => 'Pertemuan kader',
                'level' => 'desa',
                'area_id' => $this->desaA->id,
                'created_by' => $adminDesa->id,
            ]);
        }

        Bkr::create([
            'desa' => 'Bandung',
            'nama_bkr' => 'BKR Bocor',
            'no_tgl_sk' => '02/SK/BKR/2026',
            'nama_ketua_kelompok' => 'Rina Wati',
            'jumlah_anggota' => 30,
            'kegiatan' => 'Kelas pembinaan keluarga',
            'level' => 'desa',
            'area_id' => $this->desaB->id,
            'created_by' => $adminDesa->id,
        ]);

        $response = $this->actingAs($adminDesa)->get('/desa/bkr?page=2&per_page=10');

        $response->assertOk();
        $response->assertDontSee('BKR Bocor');
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Desa/Bkr/Index')
                ->has('bkrItems.data', 2)
                ->where('bkrItems.current_page', 2)
                ->where('bkrItems.per_page', 10)
                ->where('bkrItems.total', 12)
                ->where('filters.per_page', 10);
        });
    }

    #[Test]
    public function admin_desa_dapat_menambah_memperbarui_dan_menghapus_bkr(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        $this->actingAs($adminDesa)->post('/desa/bkr', [
            'desa' => 'Gombong',
            'nama_bkr' => 'BKR Anggrek',
            'no_tgl_sk' => '03/SK/BKR/2026',
            'nama_ketua_kelompok' => 'Nur Aini',
            'jumlah_anggota' => 18,
            'kegiatan' => 'Penyuluhan gizi lansia',
        ])->assertStatus(302);

        $bkr = Bkr::where('nama_bkr', 'BKR Anggrek')->firstOrFail();

        $this->actingAs($adminDesa)->put(route('desa.bkr.update', $bkr->id), [
            'desa' => 'Gombong',
            'nama_bkr' => 'BKR Anggrek',
            'no_tgl_sk' => '03/SK/BKR/2026',
            'nama_ketua_kelompok' => 'Nur Aini',
            'jumlah_anggota' => 20,
            'kegiatan' => 'Penyuluhan gizi lansia dan senam',
        ])->assertStatus(302);

        $this->assertDatabaseHas('bkrs', [
            'id' => $bkr->id,
            'jumlah_anggota' => 20,
            'kegiatan' => 'Penyuluhan gizi lansia dan senam',
        ]);

        $this->actingAs($adminDesa)->delete(route('desa.bkr.destroy', $bkr->id))
            ->assertStatus(302);

        $this->assertDatabaseMissing('bkrs', ['id' => $bkr->id]);
    }

    #[Test]
    public function pengguna_non_admin_desa_tidak_bisa_mengakses_modul_bkr_desa(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatan->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $response = $this->actingAs($adminKecamatan)->get('/desa/bkr');

        $response->assertStatus(403);
    }
}
