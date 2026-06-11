<?php

namespace Tests\Unit\Repositories;

use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\AnggotaPokja\Models\AnggotaPokja;
use App\Domains\Wilayah\CatatanKeluarga\Repositories\CatatanKeluargaRepositoryInterface;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CatatanKeluargaRepositoryDataKegiatanPkkPokjaITest extends TestCase
{
    use RefreshDatabase;

    public function test_kecamatan_pokja_i_menghasilkan_10_desa_canonical_dan_zero_fill(): void
    {
        Role::firstOrCreate(['name' => 'kecamatan-sekretaris']);

        $kecamatan = Area::create(['code' => '2000', 'name' => 'Pecalungan', 'level' => 'kecamatan']);
        $user = User::factory()->create(['scope' => 'kecamatan', 'area_id' => $kecamatan->id]);
        $user->assignRole('kecamatan-sekretaris');
        $this->actingAs($user);

        $desaNames = [
            '2001' => 'Pecalungan',
            '2002' => 'Bandung',
            '2003' => 'Gombong',
            '2004' => 'Randu',
            '2005' => 'Siguci',
            '2006' => 'Pretek',
            '2007' => 'Selokarto',
            '2008' => 'Gemuh',
            '2009' => 'Gumawang',
            '2010' => 'Keniten',
        ];

        $desaAreas = [];
        foreach ($desaNames as $code => $name) {
            $desaAreas[$code] = Area::create([
                'code' => $code,
                'name' => $name,
                'level' => 'desa',
                'parent_id' => $kecamatan->id,
            ]);
        }

        AnggotaPokja::create([
            'nama' => 'Kader Pecalungan',
            'jabatan' => 'Pokja I',
            'jenis_kelamin' => 'P',
            'tempat_lahir' => 'Batang',
            'tanggal_lahir' => '1990-01-01',
            'status_perkawinan' => 'Menikah',
            'alamat' => 'Desa Pecalungan',
            'pendidikan' => 'SMA',
            'pekerjaan' => 'Ibu Rumah Tangga',
            'keterangan' => null,
            'pokja' => 'pokja-i',
            'tahun_anggaran' => now()->year,
            'level' => 'desa',
            'area_id' => $desaAreas['2001']->id,
            'created_by' => $user->id,
        ]);

        Activity::create([
            'title' => 'KISAH gotong royong',
            'description' => 'volume',
            'uraian' => 'metode sasaran',
            'level' => 'desa',
            'group' => 'pokja-i',
            'area_id' => $desaAreas['2001']->id,
            'created_by' => $user->id,
            'tahun_anggaran' => now()->year,
            'activity_date' => now()->toDateString(),
            'status' => 'published',
        ]);

        /** @var \Illuminate\Support\Collection<int, array<string, mixed>> $items */
        $items = app(CatatanKeluargaRepositoryInterface::class)
            ->getDataKegiatanPkkPokjaIByLevelAndArea('kecamatan', $kecamatan->id);

        $this->assertCount(10, $items);
        $this->assertSame(array_values($desaNames), $items->pluck('nama_wilayah')->all());
        $this->assertSame(1, $items->first()['jumlah_kader']);
        $this->assertSame(0, $items->get(1)['jumlah_kader']);
        $this->assertSame(1, $items->sum('jumlah_kader'));
        $this->assertSame(1, $items->first()['kisah_kegiatan']);
    }

    public function test_pokja_i_menggunakan_additional_info_sebagai_prioritas(): void
    {
        Role::firstOrCreate(['name' => 'desa-sekretaris']);
        $kecamatan = Area::create(['code' => '2000', 'name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desa = Area::create(['code' => '2001', 'name' => 'Pecalungan', 'level' => 'desa', 'parent_id' => $kecamatan->id]);
        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $desa->id]);
        $user->assignRole('desa-sekretaris');
        $this->actingAs($user);

        // 1. Data lama (keyword matching)
        Activity::create([
            'title' => 'Kegiatan KISAH lama',
            'level' => 'desa',
            'group' => 'pokja-i',
            'area_id' => $desa->id,
            'created_by' => $user->id,
            'tahun_anggaran' => now()->year,
            'activity_date' => now()->toDateString(),
            'status' => 'published',
        ]);

        // 2. Data baru (additional_info)
        Activity::create([
            'title' => 'Aktivitas PKBN Baru',
            'level' => 'desa',
            'group' => 'pokja-i',
            'area_id' => $desa->id,
            'created_by' => $user->id,
            'tahun_anggaran' => now()->year,
            'activity_date' => now()->toDateString(),
            'status' => 'published',
            'additional_info' => [
                'program_category' => 'pkbn',
                'volume' => 5,
                'sasaran_jumlah' => 100,
                'metode' => 'Penyuluhan',
            ],
        ]);

        /** @var \Illuminate\Support\Collection<int, array<string, mixed>> $items */
        $items = app(CatatanKeluargaRepositoryInterface::class)
            ->getDataKegiatanPkkPokjaIByLevelAndArea('desa', $desa->id);

        $row = $items->first();

        // KISAH (fallback)
        $this->assertEquals(1, $row['kisah_kegiatan']);
        $this->assertEquals(1, $row['kisah_volume']);

        // PKBN (structured)
        $this->assertEquals(1, $row['pkbn_kegiatan']);
        $this->assertEquals(5, $row['pkbn_volume']);
        $this->assertEquals(1, $row['pkbn_metode']);
        $this->assertEquals(100, $row['pkbn_sasaran']);
    }
}
