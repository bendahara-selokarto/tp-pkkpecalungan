<?php

namespace Tests\Feature;

use App\Domains\Wilayah\Posyandu\Models\Posyandu;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class KecamatanPosyanduTest extends TestCase
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
    public function admin_kecamatan_dapat_melihat_posyandu_di_kecamatannya_sendiri(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        Posyandu::create([
            'nama_posyandu' => 'Posyandu Anyelir',
            'nama_pengelola' => 'Wati',
            'nama_sekretaris' => 'Rani',
            'jenis_posyandu' => 'Pratama',
            'jumlah_kader' => 15,
            'jenis_kegiatan' => 'Penimbangan',
            'frekuensi_layanan' => 12,
            'jumlah_pengunjung_l' => 22,
            'jumlah_pengunjung_p' => 27,
            'jumlah_petugas_l' => 2,
            'jumlah_petugas_p' => 4,
            'keterangan' => 'Monitoring kecamatan',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $adminKecamatan->id,
        ]);

        Posyandu::create([
            'nama_posyandu' => 'Posyandu Dahlia',
            'nama_pengelola' => 'Mia',
            'nama_sekretaris' => 'Rara',
            'jenis_posyandu' => 'Madya',
            'jumlah_kader' => 13,
            'jenis_kegiatan' => 'Imunisasi',
            'frekuensi_layanan' => 10,
            'jumlah_pengunjung_l' => 14,
            'jumlah_pengunjung_p' => 16,
            'jumlah_petugas_l' => 1,
            'jumlah_petugas_p' => 3,
            'keterangan' => 'Data kecamatan lain',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)->get('/kecamatan/posyandu');

        $response->assertOk();
        $response->assertSee('Posyandu Anyelir');
        $response->assertDontSee('Posyandu Dahlia');
    }

    #[Test]
    public function admin_kecamatan_tidak_bisa_melihat_detail_posyandu_kecamatan_lain(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $posyanduLuar = Posyandu::create([
            'nama_posyandu' => 'Posyandu Luar Area',
            'nama_pengelola' => 'Sari',
            'nama_sekretaris' => 'Nia',
            'jenis_posyandu' => 'Mandiri',
            'jumlah_kader' => 9,
            'jenis_kegiatan' => 'Konseling',
            'frekuensi_layanan' => 8,
            'jumlah_pengunjung_l' => 9,
            'jumlah_pengunjung_p' => 11,
            'jumlah_petugas_l' => 1,
            'jumlah_petugas_p' => 2,
            'keterangan' => 'Tidak boleh diakses',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)
            ->get(route('kecamatan.posyandu.show', $posyanduLuar->id));

        $response->assertStatus(403);
    }

    #[Test]
    public function pengguna_non_admin_kecamatan_tidak_bisa_mengakses_modul_posyandu_kecamatan(): void
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

        $response = $this->actingAs($adminDesa)->get('/kecamatan/posyandu');

        $response->assertStatus(403);
    }

    #[Test]
    public function pengguna_role_kecamatan_dengan_area_tidak_valid_tidak_bisa_mengakses_modul_posyandu_kecamatan(): void
    {
        $desa = Area::create([
            'name' => 'Bandung',
            'level' => 'desa',
            'parent_id' => $this->kecamatanA->id,
        ]);

        $userStale = User::factory()->create([
            'area_id' => $desa->id,
            'scope' => 'kecamatan',
        ]);
        $userStale->assignRole('admin-kecamatan');

        $response = $this->actingAs($userStale)->get('/kecamatan/posyandu');

        $response->assertStatus(403);
    }
}
