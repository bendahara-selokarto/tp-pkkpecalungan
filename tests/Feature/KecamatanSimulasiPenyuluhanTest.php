<?php

namespace Tests\Feature;

use App\Domains\Wilayah\Models\Area;
use App\Domains\Wilayah\SimulasiPenyuluhan\Models\SimulasiPenyuluhan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class KecamatanSimulasiPenyuluhanTest extends TestCase
{
    use RefreshDatabase;

    protected Area $kecamatanA;
    protected Area $kecamatanB;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'admin-kecamatan']);
        Role::create(['name' => 'admin-desa']);

        $this->kecamatanA = Area::create([
            'name' => 'Pecalungan',
            'level' => 'kecamatan',
        ]);

        $this->kecamatanB = Area::create([
            'name' => 'Limpung',
            'level' => 'kecamatan',
        ]);
    }

    #[Test]
    public function admin_kecamatan_dapat_melihat_simulasi_penyuluhan_di_kecamatannya_sendiri(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        SimulasiPenyuluhan::create([
            'nama_kegiatan' => 'Simulasi Evakuasi Bencana',
            'jenis_simulasi_penyuluhan' => 'Simulasi',
            'jumlah_kelompok' => 5,
            'jumlah_sosialisasi' => 2,
            'jumlah_kader_l' => 6,
            'jumlah_kader_p' => 14,
            'keterangan' => 'Koordinasi lintas desa',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $adminKecamatan->id,
        ]);

        SimulasiPenyuluhan::create([
            'nama_kegiatan' => 'Penyuluhan Sanitasi',
            'jenis_simulasi_penyuluhan' => 'Penyuluhan',
            'jumlah_kelompok' => 3,
            'jumlah_sosialisasi' => 4,
            'jumlah_kader_l' => 2,
            'jumlah_kader_p' => 11,
            'keterangan' => 'Kecamatan lain',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)->get('/kecamatan/simulasi-penyuluhan');

        $response->assertOk();
        $response->assertSee('Simulasi Evakuasi Bencana');
        $response->assertDontSee('Penyuluhan Sanitasi');
    }

    #[Test]
    public function admin_kecamatan_tidak_bisa_melihat_detail_simulasi_penyuluhan_kecamatan_lain(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $simulasiLuar = SimulasiPenyuluhan::create([
            'nama_kegiatan' => 'Simulasi Luar Area',
            'jenis_simulasi_penyuluhan' => 'Simulasi',
            'jumlah_kelompok' => 1,
            'jumlah_sosialisasi' => 1,
            'jumlah_kader_l' => 1,
            'jumlah_kader_p' => 1,
            'keterangan' => null,
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)
            ->get(route('kecamatan.simulasi-penyuluhan.show', $simulasiLuar->id));

        $response->assertStatus(403);
    }

    #[Test]
    public function pengguna_non_admin_kecamatan_tidak_bisa_mengakses_modul_simulasi_penyuluhan_kecamatan(): void
    {
        $desa = Area::create([
            'name' => 'Gombong',
            'level' => 'desa',
            'parent_id' => $this->kecamatanA->id,
        ]);

        $adminDesa = User::factory()->create([
            'area_id' => $desa->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        $response = $this->actingAs($adminDesa)->get('/kecamatan/simulasi-penyuluhan');

        $response->assertStatus(403);
    }
}

