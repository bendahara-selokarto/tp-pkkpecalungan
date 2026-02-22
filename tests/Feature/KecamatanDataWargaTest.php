<?php

namespace Tests\Feature;

use App\Domains\Wilayah\DataWarga\Models\DataWarga;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class KecamatanDataWargaTest extends TestCase
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
    public function admin_kecamatan_dapat_melihat_data_warga_di_kecamatannya_sendiri(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        DataWarga::create([
            'dasawisma' => 'Anyelir 01',
            'nama_kepala_keluarga' => 'Dewi Lestari',
            'alamat' => 'RW 01',
            'jumlah_warga_laki_laki' => 10,
            'jumlah_warga_perempuan' => 12,
            'keterangan' => null,
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $adminKecamatan->id,
        ]);

        DataWarga::create([
            'dasawisma' => 'Dahlia 04',
            'nama_kepala_keluarga' => 'Sri Handayani',
            'alamat' => 'RW 03',
            'jumlah_warga_laki_laki' => 7,
            'jumlah_warga_perempuan' => 9,
            'keterangan' => null,
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)->get('/kecamatan/data-warga');

        $response->assertOk();
        $response->assertSee('Dewi Lestari');
        $response->assertDontSee('Sri Handayani');
    }

    #[Test]
    public function admin_kecamatan_tidak_bisa_melihat_detail_data_warga_kecamatan_lain(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $dataWargaLuar = DataWarga::create([
            'dasawisma' => 'Luar 01',
            'nama_kepala_keluarga' => 'Santi',
            'alamat' => 'RW 10',
            'jumlah_warga_laki_laki' => 6,
            'jumlah_warga_perempuan' => 5,
            'keterangan' => null,
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)
            ->get(route('kecamatan.data-warga.show', $dataWargaLuar->id));

        $response->assertStatus(403);
    }

    #[Test]
    public function admin_kecamatan_dapat_menambah_dan_memperbarui_data_warga(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $this->actingAs($adminKecamatan)->post('/kecamatan/data-warga', [
            'dasawisma' => 'Anyelir 02',
            'nama_kepala_keluarga' => 'Nuryanti',
            'alamat' => 'RW 02',
            'jumlah_warga_laki_laki' => 0,
            'jumlah_warga_perempuan' => 0,
            'keterangan' => 'Input awal',
            'anggota' => [
                [
                    'nama' => 'Nina',
                    'jenis_kelamin' => 'P',
                    'tanggal_lahir' => '1998-02-22',
                ],
            ],
        ])->assertStatus(302);

        $dataWarga = DataWarga::query()
            ->where('area_id', $this->kecamatanA->id)
            ->where('nama_kepala_keluarga', 'Nuryanti')
            ->firstOrFail();

        $this->actingAs($adminKecamatan)->put(route('kecamatan.data-warga.update', $dataWarga->id), [
            'dasawisma' => 'Anyelir 02',
            'nama_kepala_keluarga' => 'Nuryanti',
            'alamat' => 'RW 02',
            'jumlah_warga_laki_laki' => 0,
            'jumlah_warga_perempuan' => 0,
            'keterangan' => 'Input revisi',
            'anggota' => [
                [
                    'nama' => 'Nina',
                    'jenis_kelamin' => 'P',
                    'tanggal_lahir' => '1998-02-22',
                ],
                [
                    'nama' => 'Niko',
                    'jenis_kelamin' => 'L',
                    'tanggal_lahir' => '1996-01-10',
                ],
            ],
        ])->assertStatus(302);

        $this->assertDatabaseHas('data_wargas', [
            'id' => $dataWarga->id,
            'jumlah_warga_laki_laki' => 1,
            'jumlah_warga_perempuan' => 1,
            'keterangan' => 'Input revisi',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
        ]);
    }

    #[Test]
    public function pengguna_non_admin_kecamatan_tidak_bisa_mengakses_modul_data_warga_kecamatan(): void
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

        $response = $this->actingAs($adminDesa)->get('/kecamatan/data-warga');

        $response->assertStatus(403);
    }

    #[Test]
    public function pengguna_role_kecamatan_dengan_area_tidak_valid_tidak_bisa_mengakses_modul_data_warga_kecamatan(): void
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

        $response = $this->actingAs($userStale)->get('/kecamatan/data-warga');

        $response->assertStatus(403);
    }
}
