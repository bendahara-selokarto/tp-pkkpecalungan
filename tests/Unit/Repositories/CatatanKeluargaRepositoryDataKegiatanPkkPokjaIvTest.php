<?php

namespace Tests\Unit\Repositories;

use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\CatatanKeluarga\Repositories\CatatanKeluargaRepositoryInterface;
use App\Domains\Wilayah\DataKegiatanWarga\Models\DataKegiatanWarga;
use App\Domains\Wilayah\DataWarga\Models\DataWarga;
use App\Domains\Wilayah\DataWarga\Models\DataWargaAnggota;
use App\Domains\Wilayah\KaderKhusus\Models\KaderKhusus;
use App\Domains\Wilayah\Models\Area;
use App\Domains\Wilayah\Posyandu\Models\Posyandu;
use App\Domains\Wilayah\ProgramPrioritas\Models\ProgramPrioritas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CatatanKeluargaRepositoryDataKegiatanPkkPokjaIvTest extends TestCase
{
    use RefreshDatabase;

    public function test_pokja_iv_mengagregasi_data_dari_berbagai_modul_spesifik(): void
    {
        Role::firstOrCreate(['name' => 'desa-sekretaris']);
        $kecamatan = Area::create(['code' => '2000', 'name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desa = Area::create(['code' => '2001', 'name' => 'Pecalungan', 'level' => 'desa', 'parent_id' => $kecamatan->id]);
        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $desa->id]);
        $user->assignRole('desa-sekretaris');
        $this->actingAs($user);

        // 1. Kader Khusus (Kesehatan)
        KaderKhusus::create([
            'nama' => 'Kader Gizi 1',
            'jenis_kader_khusus' => 'Kader Gizi',
            'level' => 'desa',
            'area_id' => $desa->id,
            'tahun_anggaran' => now()->year,
            'created_by' => $user->id,
        ]);

        // 2. Posyandu
        Posyandu::create([
            'nama_posyandu' => 'Posyandu Melati',
            'jumlah_pengunjung_l' => 5,
            'jumlah_pengunjung_p' => 5,
            'level' => 'desa',
            'area_id' => $desa->id,
            'tahun_anggaran' => now()->year,
            'created_by' => $user->id,
        ]);

        // 3. Data Kegiatan Warga (PKG/TBC)
        DataKegiatanWarga::create([
            'kegiatan' => 'Pemeriksaan Kesehatan Gratis (PKG)',
            'aktivitas' => true,
            'level' => 'desa',
            'area_id' => $desa->id,
            'tahun_anggaran' => now()->year,
            'created_by' => $user->id,
        ]);

        // 4. Data Warga (Sanitasi)
        $warga = DataWarga::create([
            'nama_kepala_keluarga' => 'Keluarga Sehat',
            'jamban' => true,
            'level' => 'desa',
            'area_id' => $desa->id,
            'tahun_anggaran' => now()->year,
            'created_by' => $user->id,
        ]);

        // 5. Data Warga Anggota (PUS/KB)
        DataWargaAnggota::create([
            'data_warga_id' => $warga->id,
            'nama' => 'Ibu PUS',
            'jenis_kelamin' => 'P',
            'umur_tahun' => 30,
            'status_perkawinan' => 'Kawin',
            'akseptor_kb' => true,
            'level' => 'desa',
            'area_id' => $desa->id,
            'tahun_anggaran' => now()->year,
            'created_by' => $user->id,
        ]);

        // 6. Program Prioritas (Unggulan)
        ProgramPrioritas::create([
            'program' => 'Kesehatan',
            'level' => 'desa',
            'area_id' => $desa->id,
            'tahun_anggaran' => now()->year,
            'created_by' => $user->id,
        ]);

        /** @var \Illuminate\Support\Collection<int, array<string, mixed>> $items */
        $items = app(CatatanKeluargaRepositoryInterface::class)
            ->getDataKegiatanPkkPokjaIvByLevelAndArea('desa', $desa->id);

        $row = $items->first();

        $this->assertEquals(1, $row['jumlah_kader_gizi']);
        $this->assertEquals(1, $row['jumlah_posyandu']);
        $this->assertEquals(10, $row['jumlah_imunisasi_vaksinasi_bayi_balita']);
        $this->assertEquals(1, $row['jumlah_pkg_klp']);
        $this->assertEquals(1, $row['jumlah_jamban']);
        $this->assertEquals(1, $row['jumlah_pus']);
        $this->assertEquals(1, $row['jumlah_akseptor_kb_p']);
        $this->assertEquals(1, $row['program_unggulan_kesehatan']);
    }
}
