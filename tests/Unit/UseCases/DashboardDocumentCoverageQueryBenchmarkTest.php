<?php

namespace Tests\Unit\UseCases;

use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\Dashboard\UseCases\BuildDashboardDocumentCoverageUseCase;
use App\Domains\Wilayah\DataWarga\Models\DataWarga;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardDocumentCoverageQueryBenchmarkTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'admin-kecamatan']);
    }

    public function test_query_agregat_dashboard_kecamatan_tetap_terkendali_pada_banyak_desa(): void
    {
        $smallUser = $this->buildKecamatanScenario('Kecamatan Kecil', 5);
        $largeUser = $this->buildKecamatanScenario('Kecamatan Besar', 40);

        $smallCount = $this->measureQueryCount($smallUser);
        $largeCount = $this->measureQueryCount($largeUser);

        $this->assertLessThanOrEqual(
            80,
            $largeCount,
            sprintf('Jumlah query dashboard kecamatan besar terlalu tinggi: %d', $largeCount)
        );
        $this->assertLessThanOrEqual(
            $smallCount + 6,
            $largeCount,
            sprintf(
                'Query bertambah terlalu besar saat desa meningkat (kecil=%d, besar=%d)',
                $smallCount,
                $largeCount
            )
        );
    }

    private function buildKecamatanScenario(string $kecamatanName, int $desaCount): User
    {
        $kecamatan = Area::create([
            'name' => $kecamatanName,
            'level' => 'kecamatan',
        ]);

        $user = User::factory()->create([
            'scope' => 'kecamatan',
            'area_id' => $kecamatan->id,
        ]);
        $user->assignRole('admin-kecamatan');

        for ($i = 1; $i <= $desaCount; $i++) {
            $desa = Area::create([
                'name' => sprintf('%s - Desa %02d', $kecamatanName, $i),
                'level' => 'desa',
                'parent_id' => $kecamatan->id,
            ]);

            Activity::create([
                'title' => sprintf('Aktivitas Desa %02d', $i),
                'level' => 'desa',
                'area_id' => $desa->id,
                'created_by' => $user->id,
                'activity_date' => now()->toDateString(),
                'status' => 'published',
            ]);

            DataWarga::create([
                'dasawisma' => sprintf('Melati %02d', $i),
                'nama_kepala_keluarga' => sprintf('KK %02d', $i),
                'alamat' => sprintf('Alamat %02d', $i),
                'jumlah_warga_laki_laki' => 2,
                'jumlah_warga_perempuan' => 2,
                'keterangan' => null,
                'level' => 'desa',
                'area_id' => $desa->id,
                'created_by' => $user->id,
            ]);
        }

        return $user;
    }

    private function measureQueryCount(User $user): int
    {
        Cache::flush();
        DB::flushQueryLog();
        DB::enableQueryLog();

        app(BuildDashboardDocumentCoverageUseCase::class)->execute($user, [
            'mode' => 'all',
            'level' => 'all',
            'sub_level' => 'all',
            'block' => 'documents',
        ]);

        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        return count($queries);
    }
}
