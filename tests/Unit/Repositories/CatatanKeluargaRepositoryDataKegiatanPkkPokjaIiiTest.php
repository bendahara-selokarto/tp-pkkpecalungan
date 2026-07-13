<?php

namespace Tests\Unit\Repositories;

use App\Domains\Wilayah\AnggotaPokja\Models\AnggotaPokja;
use App\Domains\Wilayah\CatatanKeluarga\Repositories\CatatanKeluargaRepositoryInterface;
use App\Domains\Wilayah\DataIndustriRumahTangga\Models\DataIndustriRumahTangga;
use App\Domains\Wilayah\DataKegiatanWarga\Models\DataKegiatanWarga;
use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Models\DataPemanfaatanTanahPekaranganHatinyaPkk;
use App\Domains\Wilayah\DataWarga\Models\DataWarga;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CatatanKeluargaRepositoryDataKegiatanPkkPokjaIiiTest extends TestCase
{
    use RefreshDatabase;

    public function test_pokja_iii_mengagregasi_data_dari_berbagai_modul_spesifik(): void
    {
        $this->markTestSkipped('Stale: Menunggu penyusunan ulang bertahap');
        Role::firstOrCreate(['name' => 'desa-sekretaris']);
        $kecamatan = Area::create(['code' => '2000', 'name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desa = Area::create(['code' => '2001', 'name' => 'Pecalungan', 'level' => 'desa', 'parent_id' => $kecamatan->id]);
        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $desa->id]);
        $user->assignRole('desa-sekretaris');
        $this->actingAs($user);

        // 1. Kader Pokja III
        AnggotaPokja::create([
            'nama' => 'Kader Pangan',
            'jabatan' => 'Kader Pangan',
            'jenis_kelamin' => 'P',
            'tempat_lahir' => 'Batang',
            'tanggal_lahir' => '1990-01-01',
            'status_perkawinan' => 'Menikah',
            'alamat' => 'Desa Pecalungan',
            'pendidikan' => 'SMA',
            'pekerjaan' => 'Ibu Rumah Tangga',
            'pokja' => 'pokja-iii',
            'level' => 'desa',
            'area_id' => $desa->id,
            'tahun_anggaran' => now()->year,
            'created_by' => $user->id,
        ]);

        // 2. Data Warga (Pangan & Rumah)
        DataWarga::create([
            'dasawisma' => 'Mawar 01',
            'alamat' => 'RT 01 RW 01',
            'rt' => '01',
            'rw' => '01',
            'nama_kepala_keluarga' => 'Keluarga Beras Sehat',
            'makanan_pokok_sehari_hari' => 'Beras',
            'status_rumah' => 'Sehat Layak Huni',
            'level' => 'desa',
            'area_id' => $desa->id,
            'tahun_anggaran' => now()->year,
            'created_by' => $user->id,
        ]);

        // 3. Pemanfaatan Tanah Pekarangan
        DataPemanfaatanTanahPekaranganHatinyaPkk::create([
            'kategori_pemanfaatan_lahan' => 'Peternakan',
            'komoditi' => 'Ayam',
            'jumlah_komoditi' => 50,
            'level' => 'desa',
            'area_id' => $desa->id,
            'tahun_anggaran' => now()->year,
            'created_by' => $user->id,
        ]);

        // 4. Industri Rumah Tangga
        DataIndustriRumahTangga::create([
            'kategori_jenis_industri' => 'Pangan',
            'komoditi' => 'Keripik Tempe',
            'jumlah_komoditi' => 10,
            'level' => 'desa',
            'area_id' => $desa->id,
            'tahun_anggaran' => now()->year,
            'created_by' => $user->id,
        ]);

        /** @var \Illuminate\Support\Collection<int, array<string, mixed>> $items */
        $items = app(CatatanKeluargaRepositoryInterface::class)
            ->getDataKegiatanPkkPokjaIiiByLevelAndArea('desa', $desa->id);

        $row = $items->first();

        $this->assertEquals(1, $row['jumlah_kader_pangan']);
        $this->assertEquals(1, $row['jumlah_keluarga_beras']);
        $this->assertEquals(50, $row['jumlah_peternakan']);
        $this->assertEquals(10, $row['jumlah_industri_pangan']);
        $this->assertEquals(1, $row['jumlah_rumah_sehat_layak_huni']);
    }
}
