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
        Role::create(['name' => 'desa-sekretaris']);
        Role::create(['name' => 'kecamatan-sekretaris']);
        Role::create(['name' => 'desa-pokja-i']);
        Role::create(['name' => 'kecamatan-pokja-i']);
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

    public function test_dashboard_desa_sekretaris_menghasilkan_section_1_dan_section_2_tanpa_section_3(): void
    {
        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desa = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $desa->id,
        ]);
        $user->assignRole('desa-sekretaris');

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page) {
            $page
                ->component('Dashboard')
                ->where('dashboardBlocks', function ($blocks): bool {
                    $collected = collect($blocks);
                    $section1 = $collected
                        ->where('section.key', 'sekretaris-section-1')
                        ->first();
                    $section2 = $collected
                        ->where('section.key', 'sekretaris-section-2')
                        ->first();
                    $section3 = $collected
                        ->where('section.key', 'sekretaris-section-3')
                        ->first();

                    return is_array($section1)
                        && is_array($section2)
                        && $section3 === null
                        && ($section2['sources']['filter_context']['level'] ?? null) === 'desa'
                        && ($section2['sources']['filter_context']['section2_group'] ?? null) === 'all';
                });
        });
    }

    public function test_dashboard_kecamatan_sekretaris_menghasilkan_section_2_dan_section_3_dengan_konteks_group(): void
    {
        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desa = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create([
            'scope' => 'kecamatan',
            'area_id' => $kecamatan->id,
        ]);
        $user->assignRole('kecamatan-sekretaris');

        $this->createActivity($user, 'kecamatan', $kecamatan->id, 'Aktivitas Kecamatan');
        $this->createActivity($user, 'desa', $desa->id, 'Aktivitas Desa');

        $response = $this->actingAs($user)->get(route('dashboard', [
            'section2_group' => 'pokja-i',
            'section3_group' => 'pokja-ii',
        ]));

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page) {
            $page
                ->component('Dashboard')
                ->where('dashboardBlocks', function ($blocks): bool {
                    $collected = collect($blocks);
                    $section1 = $collected
                        ->first(static fn ($block): bool => ($block['section']['key'] ?? null) === 'sekretaris-section-1');
                    $section2 = $collected
                        ->first(static fn ($block): bool => ($block['section']['key'] ?? null) === 'sekretaris-section-2');
                    $section3 = $collected
                        ->first(static fn ($block): bool => ($block['section']['key'] ?? null) === 'sekretaris-section-3');
                    $section4 = $collected
                        ->first(static fn ($block): bool => ($block['section']['key'] ?? null) === 'sekretaris-section-4');

                    return is_array($section1)
                        && is_array($section2)
                        && is_array($section3)
                        && $section4 === null
                        && ($section1['section']['source_level'] ?? null) === 'kecamatan'
                        && ($section1['charts']['by_desa']['labels'] ?? null) === ['Gombong']
                        && ($section1['charts']['by_desa']['values'] ?? null) === [1]
                        && ($section1['charts']['by_desa']['books_total'] ?? null) === [19]
                        && ($section1['charts']['by_desa']['books_filled'] ?? null) === [1]
                        && ($section2['sources']['filter_context']['level'] ?? null) === 'kecamatan'
                        && ($section2['sources']['filter_context']['section2_group'] ?? null) === 'pokja-i'
                        && ($section3['sources']['filter_context']['level'] ?? null) === 'desa'
                        && ($section3['sources']['filter_context']['section3_group'] ?? null) === 'pokja-ii';
                });
        });
    }

    public function test_dashboard_kecamatan_sekretaris_section_4_muncul_saat_filter_section_3_pokja_i_dan_anti_data_leak(): void
    {
        $kecamatanA = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $kecamatanB = Area::create(['name' => 'Limpung', 'level' => 'kecamatan']);
        $desaA1 = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatanA->id]);
        $desaA2 = Area::create(['name' => 'Bandung', 'level' => 'desa', 'parent_id' => $kecamatanA->id]);
        $desaB1 = Area::create(['name' => 'Sidomukti', 'level' => 'desa', 'parent_id' => $kecamatanB->id]);

        $user = User::factory()->create([
            'scope' => 'kecamatan',
            'area_id' => $kecamatanA->id,
        ]);
        $user->assignRole('kecamatan-sekretaris');

        $this->createDataWarga($user, 'desa', $desaA1->id, 'Kepala A1');
        $this->createDataWarga($user, 'desa', $desaA2->id, 'Kepala A2');
        $this->createDataWarga($user, 'desa', $desaB1->id, 'Kepala B1');

        $response = $this->actingAs($user)->get(route('dashboard', [
            'section2_group' => 'all',
            'section3_group' => 'pokja-i',
        ]));

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page) {
            $page
                ->component('Dashboard')
                ->where('dashboardBlocks', function ($blocks): bool {
                    $collected = collect($blocks);
                    $section4 = $collected
                        ->first(static fn ($block): bool => ($block['section']['key'] ?? null) === 'sekretaris-section-4');

                    if (! is_array($section4)) {
                        return false;
                    }

                    $items = collect($section4['charts']['coverage_per_module']['items'] ?? []);
                    $labels = $items->pluck('label')->all();
                    $totalsByLabel = $items->mapWithKeys(
                        static fn (array $item): array => [(string) ($item['label'] ?? '-') => (int) ($item['total'] ?? 0)]
                    );

                    return ($section4['group'] ?? null) === 'pokja-i'
                        && ($section4['section']['depends_on'] ?? null) === 'section3_group:pokja-i'
                        && ($section4['sources']['source_scope'] ?? null) === 'kecamatan'
                        && ($section4['sources']['source_area_type'] ?? null) === 'desa-turunan'
                        && ($section4['sources']['source_modules'] ?? null) === ['data-warga', 'data-kegiatan-warga', 'bkl', 'bkr', 'paar']
                        && ($section4['sources']['filter_context']['section3_group'] ?? null) === 'pokja-i'
                        && in_array('Gombong', $labels, true)
                        && in_array('Bandung', $labels, true)
                        && ! in_array('Sidomukti', $labels, true)
                        && ($totalsByLabel['Gombong'] ?? null) === 1
                        && ($totalsByLabel['Bandung'] ?? null) === 1;
                });
        });
    }

    public function test_dashboard_role_desa_pokja_hanya_melihat_blok_pokja_sendiri(): void
    {
        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desa = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $desa->id,
        ]);
        $user->assignRole('desa-pokja-i');

        $this->createActivity($user, 'desa', $desa->id, 'Aktivitas Pokja I');
        $this->createDataWarga($user, 'desa', $desa->id, 'Kepala Pokja I');

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page) {
            $page
                ->component('Dashboard')
                ->where('dashboardBlocks', function ($blocks): bool {
                    $collected = collect($blocks);
                    $groups = $collected
                        ->map(static fn (array $block): string => (string) ($block['group'] ?? ''))
                        ->unique()
                        ->values()
                        ->all();
                    $block = $collected->first();

                    return count($groups) === 1
                        && $groups === ['pokja-i']
                        && is_array($block)
                        && ($block['section'] ?? null) === null
                        && ($block['sources']['source_scope'] ?? null) === 'desa';
                });
        });
    }

    public function test_dashboard_role_kecamatan_pokja_menggunakan_breakdown_per_desa_dan_anti_data_leak(): void
    {
        $kecamatanA = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $kecamatanB = Area::create(['name' => 'Limpung', 'level' => 'kecamatan']);
        $desaA1 = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatanA->id]);
        $desaA2 = Area::create(['name' => 'Bandung', 'level' => 'desa', 'parent_id' => $kecamatanA->id]);
        $desaB1 = Area::create(['name' => 'Sidomukti', 'level' => 'desa', 'parent_id' => $kecamatanB->id]);

        $user = User::factory()->create([
            'scope' => 'kecamatan',
            'area_id' => $kecamatanA->id,
        ]);
        $user->assignRole('kecamatan-pokja-i');

        $this->createDataWarga($user, 'desa', $desaA1->id, 'Kepala A1');
        $this->createDataWarga($user, 'desa', $desaA2->id, 'Kepala A2');
        $this->createDataWarga($user, 'desa', $desaA2->id, 'Kepala A2-2');
        $this->createDataWarga($user, 'desa', $desaB1->id, 'Kepala B1');

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page) {
            $page
                ->component('Dashboard')
                ->where('dashboardBlocks', function ($blocks): bool {
                    $collected = collect($blocks);
                    $block = $collected->first();

                    if (! is_array($block)) {
                        return false;
                    }

                    $items = collect($block['charts']['coverage_per_module']['items'] ?? []);
                    $labels = $items->pluck('label')->all();
                    $totalsByLabel = $items->mapWithKeys(
                        static fn (array $item): array => [(string) ($item['label'] ?? '-') => (int) ($item['total'] ?? 0)]
                    );

                    return $collected->count() === 1
                        && ($block['group'] ?? null) === 'pokja-i'
                        && ($block['charts']['coverage_per_module']['dimension'] ?? null) === 'desa'
                        && ($block['sources']['source_area_type'] ?? null) === 'desa-turunan'
                        && in_array('Gombong', $labels, true)
                        && in_array('Bandung', $labels, true)
                        && ! in_array('Sidomukti', $labels, true)
                        && ($totalsByLabel['Gombong'] ?? null) === 1
                        && ($totalsByLabel['Bandung'] ?? null) === 2;
                });
        });
    }

    public function test_dashboard_query_filter_mengubah_filter_context_dan_mode_sub_level_tetap_stabil(): void
    {
        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create([
            'scope' => 'kecamatan',
            'area_id' => $kecamatan->id,
        ]);
        $user->assignRole('kecamatan-pokja-i');

        $levelResponse = $this->actingAs($user)->get(route('dashboard', [
            'mode' => 'by-level',
            'level' => 'desa',
        ]));

        $levelResponse->assertOk();
        $levelResponse->assertInertia(function (AssertableInertia $page) {
            $page
                ->component('Dashboard')
                ->where('dashboardBlocks', function ($blocks): bool {
                    $first = collect($blocks)->first();

                    return is_array($first)
                        && ($first['sources']['filter_context']['mode'] ?? null) === 'by-level'
                        && ($first['sources']['filter_context']['level'] ?? null) === 'desa'
                        && ($first['sources']['filter_context']['sub_level'] ?? null) === 'all';
                });
        });

        $subLevelResponse = $this->actingAs($user)->get(route('dashboard', [
            'mode' => 'by-sub-level',
            'sub_level' => 'desa-gombong',
        ]));

        $subLevelResponse->assertOk();
        $subLevelResponse->assertInertia(function (AssertableInertia $page) {
            $page
                ->component('Dashboard')
                ->where('dashboardBlocks', function ($blocks): bool {
                    $first = collect($blocks)->first();

                    return is_array($first)
                        && ($first['sources']['filter_context']['mode'] ?? null) === 'by-sub-level'
                        && ($first['sources']['filter_context']['sub_level'] ?? null) === 'desa-gombong';
                });
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
