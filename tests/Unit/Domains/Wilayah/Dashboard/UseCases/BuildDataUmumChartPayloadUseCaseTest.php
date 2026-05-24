<?php

namespace Tests\Unit\Domains\Wilayah\Dashboard\UseCases;

use App\Domains\Wilayah\CatatanKeluarga\Repositories\CatatanKeluargaRepositoryInterface;
use App\Domains\Wilayah\Dashboard\UseCases\BuildDataUmumChartPayloadUseCase;
use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Models\User;
use Illuminate\Support\Collection;
use Mockery;
use Tests\TestCase;

class BuildDataUmumChartPayloadUseCaseTest extends TestCase
{
    public function test_execute_returns_payload_for_kecamatan_user()
    {
        $repository = Mockery::mock(CatatanKeluargaRepositoryInterface::class);
        $user = new User(['scope' => ScopeLevel::KECAMATAN->value, 'area_id' => 1]);

        $mockData = collect([
            [
                'nama_desa_kelurahan' => 'Desa A',
                'jumlah_dusun_lingkungan' => 2,
                'jumlah_pkk_rw' => 3,
                'jumlah_pkk_rt' => 6,
                'jumlah_dasa_wisma' => 10,
                'jumlah_krt' => 45,
                'jumlah_kk' => 50,
                'jumlah_jiwa_l' => 50,
                'jumlah_jiwa_p' => 50,
                'jumlah_kader_anggota_tp_pkk_l' => 1,
                'jumlah_kader_anggota_tp_pkk_p' => 1,
                'jumlah_kader_umum_l' => 5,
                'jumlah_kader_umum_p' => 5,
                'jumlah_kader_khusus_l' => 2,
                'jumlah_kader_khusus_p' => 2,
                'jumlah_tenaga_sekretariat_honorer_l' => 1,
                'jumlah_tenaga_sekretariat_honorer_p' => 1,
                'jumlah_tenaga_sekretariat_bantuan_l' => 0,
                'jumlah_tenaga_sekretariat_bantuan_p' => 1,
            ]
        ]);

        $repository->shouldReceive('getDataUmumPkkKecamatanByLevelAndArea')
            ->once()
            ->with(ScopeLevel::DESA->value, 1)
            ->andReturn($mockData);

        $useCase = new BuildDataUmumChartPayloadUseCase($repository);
        $result = $useCase->execute($user);

        $this->assertArrayHasKey('kelompok', $result);
        $this->assertEquals(['Desa A'], $result['kelompok']['labels']);
        $this->assertEquals([10], $result['kelompok']['series']['Dasa Wisma']);
        $this->assertEquals([2], $result['kelompok']['series']['Dusun/Lingkungan']);
        $this->assertEquals([50], $result['jiwa']['series']['Laki-laki']);
        $this->assertEquals([2], $result['kader']['series']['Anggota TP PKK']);
        $this->assertEquals([2], $result['sekretariat']['series']['Honorer']);
    }

    public function test_execute_returns_payload_for_desa_user()
    {
        $repository = Mockery::mock(CatatanKeluargaRepositoryInterface::class);
        $user = new User(['scope' => ScopeLevel::DESA->value, 'area_id' => 1]);

        $mockData = collect([
            [
                'nama_dusun_lingkungan_atau_sebutan_lain' => 'Dusun 1',
                'jumlah_pkk_rw' => 2,
                'jumlah_pkk_rt' => 4,
                'jumlah_dasa_wisma' => 5,
                'jumlah_krt' => 15,
                'jumlah_kk' => 20,
                'jumlah_jiwa_l' => 20,
                'jumlah_jiwa_p' => 20,
                'jumlah_kader_anggota_tp_pkk_l' => 1,
                'jumlah_kader_anggota_tp_pkk_p' => 0,
                'jumlah_kader_umum_l' => 2,
                'jumlah_kader_umum_p' => 2,
                'jumlah_kader_khusus_l' => 1,
                'jumlah_kader_khusus_p' => 1,
                'jumlah_tenaga_sekretariat_honorer_l' => 0,
                'jumlah_tenaga_sekretariat_honorer_p' => 0,
                'jumlah_tenaga_sekretariat_bantuan_l' => 0,
                'jumlah_tenaga_sekretariat_bantuan_p' => 0,
            ]
        ]);

        $repository->shouldReceive('getDataUmumPkkByLevelAndArea')
            ->once()
            ->with(ScopeLevel::DESA->value, 1)
            ->andReturn($mockData);

        $useCase = new BuildDataUmumChartPayloadUseCase($repository);
        $result = $useCase->execute($user);

        $this->assertArrayHasKey('kelompok', $result);
        $this->assertEquals(['Dusun 1'], $result['kelompok']['labels']);
        $this->assertEquals([5], $result['kelompok']['series']['Dasa Wisma']);
        $this->assertArrayNotHasKey('Dusun/Lingkungan', $result['kelompok']['series']);
        $this->assertEquals([20], $result['jiwa']['series']['Perempuan']);
        $this->assertEquals([1], $result['kader']['series']['Anggota TP PKK']);
    }
}
