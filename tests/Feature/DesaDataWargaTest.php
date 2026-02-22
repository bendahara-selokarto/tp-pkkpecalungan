<?php

namespace Tests\Feature;

use App\Domains\Wilayah\DataWarga\Models\DataWarga;
use App\Domains\Wilayah\DataWarga\Models\DataWargaAnggota;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DesaDataWargaTest extends TestCase
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
    public function admin_desa_dapat_melihat_daftar_data_warga_di_desanya_sendiri(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        DataWarga::create([
            'dasawisma' => 'Mawar 01',
            'nama_kepala_keluarga' => 'Siti Aminah',
            'alamat' => 'RT 01 RW 02',
            'jumlah_warga_laki_laki' => 2,
            'jumlah_warga_perempuan' => 3,
            'keterangan' => null,
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
        ]);

        DataWarga::create([
            'dasawisma' => 'Melati 03',
            'nama_kepala_keluarga' => 'Rina Wati',
            'alamat' => 'RT 03 RW 01',
            'jumlah_warga_laki_laki' => 1,
            'jumlah_warga_perempuan' => 2,
            'keterangan' => null,
            'level' => 'desa',
            'area_id' => $this->desaB->id,
            'created_by' => $adminDesa->id,
        ]);

        $response = $this->actingAs($adminDesa)->get('/desa/data-warga');

        $response->assertOk();
        $response->assertSee('Siti Aminah');
        $response->assertDontSee('Rina Wati');
    }

    #[Test]
    public function admin_desa_dapat_menambah_memperbarui_dan_menghapus_data_warga(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        $this->actingAs($adminDesa)->post('/desa/data-warga', [
            'dasawisma' => 'Anggrek 02',
            'nama_kepala_keluarga' => 'Nur Aini',
            'alamat' => 'RT 02 RW 05',
            'jumlah_warga_laki_laki' => 2,
            'jumlah_warga_perempuan' => 2,
            'keterangan' => 'Data awal',
        ])->assertStatus(302);

        $dataWarga = DataWarga::where('nama_kepala_keluarga', 'Nur Aini')->firstOrFail();

        $this->actingAs($adminDesa)->put(route('desa.data-warga.update', $dataWarga->id), [
            'dasawisma' => 'Anggrek 02',
            'nama_kepala_keluarga' => 'Nur Aini',
            'alamat' => 'RT 02 RW 05',
            'jumlah_warga_laki_laki' => 3,
            'jumlah_warga_perempuan' => 2,
            'keterangan' => 'Perubahan warga laki-laki',
        ])->assertStatus(302);

        $this->assertDatabaseHas('data_wargas', [
            'id' => $dataWarga->id,
            'jumlah_warga_laki_laki' => 3,
            'jumlah_warga_perempuan' => 2,
        ]);

        $this->actingAs($adminDesa)->delete(route('desa.data-warga.destroy', $dataWarga->id))
            ->assertStatus(302);

        $this->assertDatabaseMissing('data_wargas', ['id' => $dataWarga->id]);
    }

    #[Test]
    public function admin_desa_dapat_menyimpan_detail_anggota_dan_summary_otomatis(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        $this->actingAs($adminDesa)->post('/desa/data-warga', [
            'dasawisma' => 'Melati 04',
            'nama_kepala_keluarga' => 'Sulastri',
            'alamat' => 'RT 04 RW 02',
            'jumlah_warga_laki_laki' => 0,
            'jumlah_warga_perempuan' => 0,
            'keterangan' => 'Header dibuat dari detail anggota',
            'anggota' => [
                [
                    'nama' => 'Andi',
                    'jenis_kelamin' => 'L',
                    'ikut_koperasi' => true,
                ],
                [
                    'nama' => 'Ana',
                    'jenis_kelamin' => 'P',
                    'ikut_paud' => true,
                ],
                [
                    'nama' => 'Budi',
                    'jenis_kelamin' => 'L',
                    'aktif_posyandu' => true,
                ],
            ],
        ])->assertStatus(302);

        $dataWarga = DataWarga::where('nama_kepala_keluarga', 'Sulastri')->firstOrFail();

        $this->assertSame(2, $dataWarga->jumlah_warga_laki_laki);
        $this->assertSame(1, $dataWarga->jumlah_warga_perempuan);
        $this->assertSame(3, DataWargaAnggota::query()->where('data_warga_id', $dataWarga->id)->count());

        $this->assertDatabaseHas('data_warga_anggotas', [
            'data_warga_id' => $dataWarga->id,
            'nama' => 'Ana',
            'jenis_kelamin' => 'P',
            'ikut_paud' => true,
        ]);

        $this->actingAs($adminDesa)->put(route('desa.data-warga.update', $dataWarga->id), [
            'dasawisma' => 'Melati 04',
            'nama_kepala_keluarga' => 'Sulastri',
            'alamat' => 'RT 04 RW 02',
            'jumlah_warga_laki_laki' => 99,
            'jumlah_warga_perempuan' => 99,
            'keterangan' => 'Summary harus dihitung ulang',
            'anggota' => [
                [
                    'nama' => 'Citra',
                    'jenis_kelamin' => 'P',
                    'ikut_bkb' => true,
                ],
            ],
        ])->assertStatus(302);

        $dataWarga->refresh();

        $this->assertSame(0, $dataWarga->jumlah_warga_laki_laki);
        $this->assertSame(1, $dataWarga->jumlah_warga_perempuan);
        $this->assertSame(1, DataWargaAnggota::query()->where('data_warga_id', $dataWarga->id)->count());

        $this->assertDatabaseMissing('data_warga_anggotas', [
            'data_warga_id' => $dataWarga->id,
            'nama' => 'Andi',
        ]);
    }

    #[Test]
    public function pengguna_non_admin_desa_tidak_bisa_mengakses_modul_data_warga_desa(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatan->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $response = $this->actingAs($adminKecamatan)->get('/desa/data-warga');

        $response->assertStatus(403);
    }

    #[Test]
    public function pengguna_role_desa_dengan_area_tidak_valid_tidak_bisa_mengakses_modul_data_warga_desa(): void
    {
        $userStale = User::factory()->create([
            'area_id' => $this->kecamatan->id,
            'scope' => 'desa',
        ]);
        $userStale->assignRole('admin-desa');

        $response = $this->actingAs($userStale)->get('/desa/data-warga');

        $response->assertStatus(403);
    }

    #[Test]
    public function tanggal_lahir_anggota_harus_format_yyyy_mm_dd_pada_store_dan_update(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        $this->actingAs($adminDesa)->post('/desa/data-warga', [
            'dasawisma' => 'Melati 09',
            'nama_kepala_keluarga' => 'Sukmawati',
            'alamat' => 'RT 09 RW 03',
            'jumlah_warga_laki_laki' => 1,
            'jumlah_warga_perempuan' => 1,
            'keterangan' => null,
            'anggota' => [
                [
                    'nama' => 'Ani',
                    'tanggal_lahir' => '22/02/2026',
                ],
            ],
        ])->assertSessionHasErrors(['anggota.0.tanggal_lahir']);

        $dataWarga = DataWarga::create([
            'dasawisma' => 'Melati 10',
            'nama_kepala_keluarga' => 'Sukmawati 2',
            'alamat' => 'RT 10 RW 03',
            'jumlah_warga_laki_laki' => 1,
            'jumlah_warga_perempuan' => 1,
            'keterangan' => null,
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
        ]);

        $this->actingAs($adminDesa)->put(route('desa.data-warga.update', $dataWarga->id), [
            'dasawisma' => 'Melati 10',
            'nama_kepala_keluarga' => 'Sukmawati 2',
            'alamat' => 'RT 10 RW 03',
            'jumlah_warga_laki_laki' => 1,
            'jumlah_warga_perempuan' => 1,
            'keterangan' => null,
            'anggota' => [
                [
                    'nama' => 'Budi',
                    'tanggal_lahir' => '22/02/2026',
                ],
            ],
        ])->assertSessionHasErrors(['anggota.0.tanggal_lahir']);
    }
}
