<?php

namespace Tests\Unit\UseCases;

use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\AgendaSurat\Models\AgendaSurat;
use App\Domains\Wilayah\Dashboard\Repositories\DashboardDocumentCoverageRepositoryInterface;
use App\Domains\Wilayah\Dashboard\UseCases\BuildDashboardDocumentCoverageUseCase;
use App\Domains\Wilayah\DataWarga\Models\DataWarga;
use App\Domains\Wilayah\Models\Area;
use App\Domains\Wilayah\Services\RoleMenuVisibilityService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BuildDashboardDocumentCoverageUseCaseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        \Spatie\Permission\Models\Role::create(['name' => 'desa-sekretaris']);
    }

    public function test_use_case_menghitung_agregasi_per_modul_dan_per_lampiran(): void
    {
        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desa = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $desa->id,
            'active_budget_year' => (int) now()->format('Y'),
        ]);
        $user->assignRole('desa-sekretaris');

        Activity::create([
            'title' => 'Aktivitas A',
            'level' => 'desa',
            'area_id' => $desa->id,
            'created_by' => $user->id,
            'activity_date' => now()->toDateString(),
            'status' => 'published',
        ]);

        AgendaSurat::create([
            'jenis_surat' => 'masuk',
            'tanggal_terima' => now()->toDateString(),
            'tanggal_surat' => now()->toDateString(),
            'nomor_surat' => 'A-001',
            'asal_surat' => 'Asal',
            'dari' => 'Dari',
            'kepada' => 'Kepada',
            'perihal' => 'Perihal',
            'lampiran' => null,
            'diteruskan_kepada' => null,
            'tembusan' => null,
            'keterangan' => null,
            'level' => 'desa',
            'area_id' => $desa->id,
            'created_by' => $user->id,
            'tahun_anggaran' => $user->active_budget_year,
        ]);

        DataWarga::create([
            'dasawisma' => 'Melati',
            'nama_kepala_keluarga' => 'Kepala A',
            'alamat' => 'Alamat A',
            'jumlah_warga_laki_laki' => 1,
            'jumlah_warga_perempuan' => 1,
            'keterangan' => null,
            'level' => 'desa',
            'area_id' => $desa->id,
            'created_by' => $user->id,
        ]);

        $payload = app(BuildDashboardDocumentCoverageUseCase::class)->execute($user);
        
        $visibility = app(RoleMenuVisibilityService::class)->resolveForScope($user, 'desa');
        $expectedTotalBooks = count(array_intersect(
            app(DashboardDocumentCoverageRepositoryInterface::class)->trackedModuleSlugs(),
            array_keys($visibility['modules'])
        ));

        $this->assertSame($expectedTotalBooks, $payload['stats']['total_buku_tracked']);
        $this->assertSame(4, $payload['stats']['buku_terisi']);
        $this->assertSame($expectedTotalBooks - 4, $payload['stats']['buku_belum_terisi']);
        $this->assertSame(4, $payload['stats']['total_entri_buku']);

        $this->assertSame([4, 0], $payload['charts']['level_distribution']['values']);

        $items = collect($payload['charts']['coverage_per_buku']['items'])->keyBy('slug');

        $this->assertSame(1, $items->get('activities')['total']);
        $this->assertSame(1, $items->get('agenda-surat')['total']);
        $this->assertSame(1, $items->get('data-warga')['total']);
        $this->assertSame(1, $items->get('catatan-keluarga')['total']);
    }

    public function test_use_case_cache_dashboard_terinvalidasi_otomatis_saat_data_berubah(): void
    {
        $desa = Area::create(['name' => 'Gombong', 'level' => 'desa']);
        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $desa->id]);
        $user->assignRole('desa-sekretaris');

        $useCase = app(BuildDashboardDocumentCoverageUseCase::class);

        $payload1 = $useCase->execute($user);
        $this->assertSame(0, $payload1['stats']['total_entri_buku']);

        Activity::create([
            'title' => 'Aktivitas Baru',
            'level' => 'desa',
            'area_id' => $desa->id,
            'created_by' => $user->id,
            'activity_date' => now()->toDateString(),
            'status' => 'published',
        ]);

        $payload2 = $useCase->execute($user);
        $this->assertSame(1, $payload2['stats']['total_entri_buku']);
    }

    public function test_use_case_cache_key_memisahkan_role_signature_dan_filter_signature(): void
    {
        $desa = Area::create(['name' => 'Gombong', 'level' => 'desa']);
        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $desa->id]);

        $useCase = app(BuildDashboardDocumentCoverageUseCase::class);

        $reflection = new \ReflectionClass($useCase);
        $method = $reflection->getMethod('buildCacheKey');
        $method->setAccessible(true);

        $key1 = $method->invoke($useCase, $user, 'desa', $desa->id, [], 1, 2026);
        $key2 = $method->invoke($useCase, $user, 'desa', $desa->id, ['mode' => 'recap'], 1, 2026);

        $this->assertNotEquals($key1, $key2);

        $user2 = User::factory()->create(['scope' => 'desa', 'area_id' => $desa->id]);
        $user2->assignRole('desa-sekretaris');
        $key3 = $method->invoke($useCase, $user2, 'desa', $desa->id, [], 1, 2026);

        $this->assertNotEquals($key1, $key3);
    }

    public function test_use_case_hanya_menghitung_data_tahun_anggaran_aktif(): void
    {
        $desa = Area::create(['name' => 'Gombong', 'level' => 'desa']);
        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $desa->id,
            'active_budget_year' => 2026
        ]);
        $user->assignRole('desa-sekretaris');

        Activity::create([
            'title' => 'Aktivitas 2026',
            'level' => 'desa',
            'area_id' => $desa->id,
            'created_by' => $user->id,
            'activity_date' => '2026-05-20',
            'status' => 'published',
        ]);

        // This should not be counted if the model supports budget year filtering
        // Note: Activity model uses activity_date to filter budget year in countModelByScope
        Activity::create([
            'title' => 'Aktivitas 2025',
            'level' => 'desa',
            'area_id' => $desa->id,
            'created_by' => $user->id,
            'activity_date' => '2025-05-20',
            'status' => 'published',
        ]);

        $payload = app(BuildDashboardDocumentCoverageUseCase::class)->execute($user);
        $this->assertSame(1, $payload['stats']['total_entri_buku']);
    }
}
