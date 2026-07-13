<?php

namespace Tests\Unit\Repositories;

use App\Domains\Wilayah\BkbKegiatan\Models\BkbKegiatan;
use App\Domains\Wilayah\CatatanKeluarga\Repositories\CatatanKeluargaRepositoryInterface;
use App\Domains\Wilayah\KaderKhusus\Models\KaderKhusus;
use App\Domains\Wilayah\KejarPaket\Models\KejarPaket;
use App\Domains\Wilayah\Koperasi\Models\Koperasi;
use App\Domains\Wilayah\LiterasiWarga\Models\LiterasiWarga;
use App\Domains\Wilayah\Models\Area;
use App\Domains\Wilayah\PelatihanKaderPokjaIi\Models\PelatihanKaderPokjaIi;
use App\Domains\Wilayah\PraKoperasiUp2k\Models\PraKoperasiUp2k;
use App\Domains\Wilayah\TamanBacaan\Models\TamanBacaan;
use App\Domains\Wilayah\TutorKhusus\Models\TutorKhusus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CatatanKeluargaRepositoryDataKegiatanPkkPokjaIiTest extends TestCase
{
    use RefreshDatabase;

    public function test_pokja_ii_mengagregasi_data_dari_berbagai_modul_spesifik(): void
    {
        Role::firstOrCreate(['name' => 'desa-sekretaris']);
        $kecamatan = Area::create(['code' => '2000', 'name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desa = Area::create(['code' => '2001', 'name' => 'Pecalungan', 'level' => 'desa', 'parent_id' => $kecamatan->id]);
        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $desa->id]);
        $user->assignRole('desa-sekretaris');
        $this->actingAs($user);

        // 1. Literasi Warga
        LiterasiWarga::create([
            'jumlah_tiga_buta' => 10,
            'level' => 'desa',
            'area_id' => $desa->id,
            'tahun_anggaran' => now()->year,
            'created_by' => $user->id,
        ]);

        // 2. Kejar Paket
        KejarPaket::create([
            'nama_kejar_paket' => 'Paket A Desa',
            'jenis_kejar_paket' => 'Paket A',
            'jumlah_warga_belajar_l' => 5,
            'jumlah_warga_belajar_p' => 5,
            'level' => 'desa',
            'area_id' => $desa->id,
            'tahun_anggaran' => now()->year,
            'created_by' => $user->id,
        ]);

        // 3. Taman Bacaan
        TamanBacaan::create([
            'nama_taman_bacaan' => 'Taman Baca Desa',
            'nama_pengelola' => 'Pengelola 1',
            'level' => 'desa',
            'area_id' => $desa->id,
            'tahun_anggaran' => now()->year,
            'created_by' => $user->id,
        ]);

        // 4. BKB Kegiatan
        BkbKegiatan::create([
            'jumlah_kelompok' => 1,
            'jumlah_ibu_peserta' => 20,
            'jumlah_ape_set' => 2,
            'jumlah_kelompok_simulasi' => 1,
            'level' => 'desa',
            'area_id' => $desa->id,
            'tahun_anggaran' => now()->year,
            'created_by' => $user->id,
        ]);

        // 5. Tutor Khusus
        TutorKhusus::create([
            'jenis_tutor' => 'KF',
            'jumlah_tutor' => 3,
            'level' => 'desa',
            'area_id' => $desa->id,
            'tahun_anggaran' => now()->year,
            'created_by' => $user->id,
        ]);

        // 6. Kader Khusus
        KaderKhusus::create([
            'nama' => 'Kader BKB 1',
            'jenis_kelamin' => 'P',
            'jenis_kader_khusus' => 'Kader BKB',
            'level' => 'desa',
            'area_id' => $desa->id,
            'tahun_anggaran' => now()->year,
            'created_by' => $user->id,
        ]);

        // 7. Pelatihan Kader
        PelatihanKaderPokjaIi::create([
            'kategori_pelatihan' => 'LP3',
            'jumlah_kader' => 5,
            'level' => 'desa',
            'area_id' => $desa->id,
            'tahun_anggaran' => now()->year,
            'created_by' => $user->id,
        ]);

        // 8. Pra Koperasi (UP2K)
        PraKoperasiUp2k::create([
            'tingkat' => 'Pemula',
            'jumlah_kelompok' => 2,
            'jumlah_peserta' => 10,
            'level' => 'desa',
            'area_id' => $desa->id,
            'tahun_anggaran' => now()->year,
            'created_by' => $user->id,
        ]);

        // 9. Koperasi Berbadan Hukum
        Koperasi::create([
            'nama_koperasi' => 'Koperasi Sejahtera',
            'berbadan_hukum' => true,
            'jumlah_anggota_l' => 15,
            'jumlah_anggota_p' => 15,
            'level' => 'desa',
            'area_id' => $desa->id,
            'tahun_anggaran' => now()->year,
            'created_by' => $user->id,
        ]);

        /** @var \Illuminate\Support\Collection<int, array<string, mixed>> $items */
        $items = app(CatatanKeluargaRepositoryInterface::class)
            ->getDataKegiatanPkkPokjaIiByLevelAndArea('desa', $desa->id);

        $row = $items->first();

        $this->assertEquals(10, $row['jumlah_warga_tiga_buta']);
        $this->assertEquals(1, $row['paket_a_klp']);
        $this->assertEquals(10, $row['paket_a_warga']);
        $this->assertEquals(1, $row['taman_baca']);
        $this->assertEquals(1, $row['bkb_klp']);
        $this->assertEquals(20, $row['bkb_ibu_peserta']);
        $this->assertEquals(2, $row['bkb_ape_set']);
        $this->assertEquals(1, $row['bkb_kelompok_simulasi']);
        $this->assertEquals(3, $row['tutor_kf']);
        $this->assertEquals(1, $row['kader_bkb']);
        $this->assertEquals(5, $row['pelatihan_lp3']);
        $this->assertEquals(2, $row['pra_koperasi_pemula_klp']);
        $this->assertEquals(10, $row['pra_koperasi_pemula_peserta']);
        $this->assertEquals(1, $row['koperasi_berbadan_hukum_klp']);
        $this->assertEquals(30, $row['koperasi_berbadan_hukum_anggota']);
    }
}
