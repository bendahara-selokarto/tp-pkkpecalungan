<?php

namespace Tests\Unit\UseCases;

use App\Domains\Wilayah\CatatanKeluarga\UseCases\ListScopedCatatanKeluargaUseCase;
use App\Domains\Wilayah\DataKegiatanWarga\Models\DataKegiatanWarga;
use App\Domains\Wilayah\DataWarga\Models\DataWarga;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ListScopedCatatanKeluargaUseCaseTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function use_case_hanya_mengembalikan_rekap_dari_area_pengguna_sendiri(): void
    {
        Role::create(['name' => 'admin-desa']);

        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desaA = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);
        $desaB = Area::create(['name' => 'Bandung', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $desaA->id]);
        $user->assignRole('admin-desa');

        DataWarga::create([
            'dasawisma' => 'Melati 1',
            'nama_kepala_keluarga' => 'Kepala A',
            'alamat' => 'Alamat A',
            'jumlah_warga_laki_laki' => 2,
            'jumlah_warga_perempuan' => 1,
            'keterangan' => 'Catatan A',
            'level' => 'desa',
            'area_id' => $desaA->id,
            'created_by' => $user->id,
        ]);

        DataWarga::create([
            'dasawisma' => 'Melati 2',
            'nama_kepala_keluarga' => 'Kepala B',
            'alamat' => 'Alamat B',
            'jumlah_warga_laki_laki' => 1,
            'jumlah_warga_perempuan' => 1,
            'keterangan' => 'Catatan B',
            'level' => 'desa',
            'area_id' => $desaB->id,
            'created_by' => $user->id,
        ]);

        DataKegiatanWarga::create([
            'kegiatan' => 'Kerja Bakti',
            'aktivitas' => true,
            'keterangan' => null,
            'level' => 'desa',
            'area_id' => $desaA->id,
            'created_by' => $user->id,
        ]);

        DataKegiatanWarga::create([
            'kegiatan' => 'Kerja Bakti',
            'aktivitas' => false,
            'keterangan' => null,
            'level' => 'desa',
            'area_id' => $desaB->id,
            'created_by' => $user->id,
        ]);

        $this->actingAs($user);
        $useCase = app(ListScopedCatatanKeluargaUseCase::class);

        $result = $useCase->execute('desa', 10);

        $this->assertSame(1, $result->total());
        $this->assertCount(1, $result->items());
        $this->assertSame('Kepala A', $result->items()[0]['nama_kepala_rumah_tangga']);
        $this->assertSame('Ya', $result->items()[0]['kerja_bakti']);

        $allResult = $useCase->executeAll('desa');
        $this->assertCount(1, $allResult);
        $this->assertSame('Kepala A', $allResult[0]['nama_kepala_rumah_tangga']);
    }
}
