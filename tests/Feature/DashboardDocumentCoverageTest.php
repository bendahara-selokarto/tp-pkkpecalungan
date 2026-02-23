<?php

namespace Tests\Feature;

use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\AgendaSurat\Models\AgendaSurat;
use App\Domains\Wilayah\DataWarga\Models\DataWarga;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardDocumentCoverageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'admin-desa']);
        Role::create(['name' => 'admin-kecamatan']);
    }

    public function test_dashboard_coverage_dokumen_pengguna_desa_hanya_menghitung_data_desanya_sendiri(): void
    {
        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desaA = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);
        $desaB = Area::create(['name' => 'Bandung', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $desaA->id,
        ]);
        $user->assignRole('admin-desa');

        $this->createActivity($user, 'desa', $desaA->id, 'Aktivitas A');
        $this->createActivity($user, 'desa', $desaB->id, 'Aktivitas B');
        $this->createAgendaSurat($user, 'desa', $desaA->id, 'A-001');
        $this->createAgendaSurat($user, 'desa', $desaB->id, 'B-001');
        $this->createDataWarga($user, 'desa', $desaA->id, 'Kepala A');
        $this->createDataWarga($user, 'desa', $desaB->id, 'Kepala B');

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page) {
            $page
                ->component('Dashboard')
                ->where('dashboardStats.documents.total_buku_tracked', 19)
                ->where('dashboardStats.documents.buku_terisi', 4)
                ->where('dashboardStats.documents.buku_belum_terisi', 15)
                ->where('dashboardStats.documents.total_entri_buku', 4)
                ->where('dashboardBlocks', function ($blocks): bool {
                    $collected = collect($blocks);
                    if ($collected->isEmpty()) {
                        return false;
                    }

                    return $collected->contains(function ($block): bool {
                        return is_array($block)
                            && is_array($block['sources'] ?? null)
                            && ($block['sources']['source_scope'] ?? null) === 'desa'
                            && is_array($block['sources']['source_modules'] ?? null);
                    });
                })
                ->where('dashboardCharts.documents.level_distribution.values', [4, 0])
                ->where('dashboardCharts.documents.coverage_per_lampiran.values', [0, 1, 0, 0, 1, 1, 1])
                ->where('dashboardCharts.documents.coverage_per_buku.items', function ($items): bool {
                    $bySlug = collect($items)->keyBy('slug');

                    return ($bySlug->get('activities')['total'] ?? null) === 1
                        && ($bySlug->get('agenda-surat')['total'] ?? null) === 1
                        && ($bySlug->get('data-warga')['total'] ?? null) === 1
                        && ($bySlug->get('catatan-keluarga')['total'] ?? null) === 1;
                });
        });
    }

    public function test_dashboard_coverage_dokumen_pengguna_kecamatan_mengikuti_kontrak_scope_per_modul(): void
    {
        $kecamatanA = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $kecamatanB = Area::create(['name' => 'Limpung', 'level' => 'kecamatan']);
        $desaA = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatanA->id]);
        $desaB = Area::create(['name' => 'Kalisalak', 'level' => 'desa', 'parent_id' => $kecamatanB->id]);

        $user = User::factory()->create([
            'scope' => 'kecamatan',
            'area_id' => $kecamatanA->id,
        ]);
        $user->assignRole('admin-kecamatan');

        $this->createActivity($user, 'kecamatan', $kecamatanA->id, 'Aktivitas Kecamatan A');
        $this->createActivity($user, 'desa', $desaA->id, 'Aktivitas Desa A');
        $this->createActivity($user, 'kecamatan', $kecamatanB->id, 'Aktivitas Kecamatan B');
        $this->createAgendaSurat($user, 'kecamatan', $kecamatanA->id, 'A-001');
        $this->createAgendaSurat($user, 'kecamatan', $kecamatanB->id, 'B-001');
        $this->createDataWarga($user, 'desa', $desaA->id, 'Kepala A');
        $this->createDataWarga($user, 'desa', $desaB->id, 'Kepala B');

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page) {
            $page
                ->component('Dashboard')
                ->where('dashboardStats.documents.total_buku_tracked', 19)
                ->where('dashboardStats.documents.buku_terisi', 2)
                ->where('dashboardStats.documents.buku_belum_terisi', 17)
                ->where('dashboardStats.documents.total_entri_buku', 3)
                ->where('dashboardBlocks', function ($blocks): bool {
                    $collected = collect($blocks);
                    if ($collected->isEmpty()) {
                        return false;
                    }

                    return $collected->contains(function ($block): bool {
                        return is_array($block)
                            && is_array($block['sources'] ?? null)
                            && ($block['sources']['source_scope'] ?? null) === 'kecamatan'
                            && ($block['sources']['source_area_type'] ?? null) === 'area-sendiri+desa-turunan';
                    });
                })
                ->where('dashboardCharts.documents.level_distribution.values', [1, 2])
                ->where('dashboardCharts.documents.coverage_per_lampiran.values', [0, 1, 0, 0, 2, 0, 0])
                ->where('dashboardCharts.documents.coverage_per_buku.items', function ($items): bool {
                    $bySlug = collect($items)->keyBy('slug');

                    return ($bySlug->get('activities')['total'] ?? null) === 2
                        && ($bySlug->get('agenda-surat')['total'] ?? null) === 1
                        && ($bySlug->get('data-warga')['total'] ?? null) === 0
                        && ($bySlug->get('catatan-keluarga')['total'] ?? null) === 0;
                });
        });
    }

    public function test_dashboard_coverage_dokumen_tetap_nol_ketika_metadata_scope_stale(): void
    {
        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desa = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create([
            'scope' => 'kecamatan',
            'area_id' => $kecamatan->id,
        ]);
        $user->assignRole('admin-desa');

        $this->createActivity($user, 'kecamatan', $kecamatan->id, 'Aktivitas Kecamatan');
        $this->createActivity($user, 'desa', $desa->id, 'Aktivitas Desa');
        $this->createAgendaSurat($user, 'kecamatan', $kecamatan->id, 'A-001');
        $this->createDataWarga($user, 'desa', $desa->id, 'Kepala A');

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page) {
            $page
                ->component('Dashboard')
                ->where('auth.user.scope', null)
                ->where('dashboardStats.documents.total_buku_tracked', 19)
                ->where('dashboardStats.documents.buku_terisi', 0)
                ->where('dashboardStats.documents.buku_belum_terisi', 19)
                ->where('dashboardStats.documents.total_entri_buku', 0)
                ->where('dashboardBlocks', [])
                ->where('dashboardCharts.documents.level_distribution.values', [0, 0])
                ->where('dashboardCharts.documents.coverage_per_lampiran.values', [0, 0, 0, 0, 0, 0, 0]);
        });
    }

    private function createActivity(User $user, string $level, int $areaId, string $title): void
    {
        Activity::create([
            'title' => $title,
            'level' => $level,
            'area_id' => $areaId,
            'created_by' => $user->id,
            'activity_date' => now()->toDateString(),
            'status' => 'published',
        ]);
    }

    private function createAgendaSurat(User $user, string $level, int $areaId, string $nomorSurat): void
    {
        AgendaSurat::create([
            'jenis_surat' => 'masuk',
            'tanggal_terima' => now()->toDateString(),
            'tanggal_surat' => now()->toDateString(),
            'nomor_surat' => $nomorSurat,
            'asal_surat' => 'Asal',
            'dari' => 'Dari',
            'kepada' => 'Kepada',
            'perihal' => 'Perihal',
            'lampiran' => null,
            'diteruskan_kepada' => null,
            'tembusan' => null,
            'keterangan' => null,
            'level' => $level,
            'area_id' => $areaId,
            'created_by' => $user->id,
        ]);
    }

    private function createDataWarga(User $user, string $level, int $areaId, string $kepalaKeluarga): void
    {
        DataWarga::create([
            'dasawisma' => 'Melati',
            'nama_kepala_keluarga' => $kepalaKeluarga,
            'alamat' => 'Alamat',
            'jumlah_warga_laki_laki' => 2,
            'jumlah_warga_perempuan' => 1,
            'keterangan' => null,
            'level' => $level,
            'area_id' => $areaId,
            'created_by' => $user->id,
        ]);
    }
}
