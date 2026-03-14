<?php

namespace Tests\Feature;

use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\AgendaSurat\Models\AgendaSurat;
use App\Domains\Wilayah\Dashboard\Repositories\DashboardDocumentCoverageRepositoryInterface;
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

        Role::create(['name' => 'desa-sekretaris']);
        Role::create(['name' => 'kecamatan-sekretaris']);
        Role::create(['name' => 'desa-sekretaris']);
        Role::create(['name' => 'kecamatan-sekretaris']);
        Role::create(['name' => 'desa-pokja-i']);
        Role::create(['name' => 'desa-pokja-ii']);
        Role::create(['name' => 'desa-pokja-iii']);
        Role::create(['name' => 'desa-pokja-iv']);
        Role::create(['name' => 'kecamatan-pokja-i']);
        Role::create(['name' => 'kecamatan-pokja-ii']);
        Role::create(['name' => 'kecamatan-pokja-iii']);
        Role::create(['name' => 'kecamatan-pokja-iv']);
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
        $user->assignRole('desa-sekretaris');

        $this->createActivity($user, 'desa', $desaA->id, 'Aktivitas A');
        $this->createActivity($user, 'desa', $desaB->id, 'Aktivitas B');
        $this->createAgendaSurat($user, 'desa', $desaA->id, 'A-001');
        $this->createAgendaSurat($user, 'desa', $desaB->id, 'B-001');
        $this->createDataWarga($user, 'desa', $desaA->id, 'Kepala A');
        $this->createDataWarga($user, 'desa', $desaB->id, 'Kepala B');

        $totalBooks = $this->expectedBooksTotal();
        $expectedRemaining = $totalBooks - 4;

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page) use ($totalBooks, $expectedRemaining): void {
            $page
                ->component('Dashboard')
                ->missing('dashboardStats')
                ->missing('dashboardCharts')
                ->where('dashboardContext.tahun_anggaran', (string) now()->format('Y'))
                ->missing('dashboardBlocks')

            $this->assertDeferredDashboardBlocks($page, function ($blocks): bool {
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
            });
        });
    }

    public function test_dashboard_blocks_dimuat_sebagai_deferred_prop_setelah_first_paint(): void
    {
        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desa = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $desa->id,
        ]);
        $user->assignRole('desa-sekretaris');

        $this->createActivity($user, 'desa', $desa->id, 'Aktivitas A');
        $this->createAgendaSurat($user, 'desa', $desa->id, 'A-001');

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Dashboard')
                ->missing('dashboardStats')
                ->missing('dashboardCharts')
                ->missing('dashboardBlocks');

            $page->loadDeferredProps('dashboard-blocks', function (AssertableInertia $reload): void {
                $reload
                    ->has('dashboardBlocks')
                    ->missing('dashboardStats')
                    ->missing('dashboardCharts')
                    ->missing('dashboardContext');
            });
        });
    }

    public function test_dashboard_partial_reload_hanya_mengembalikan_prop_dashboard_yang_diminta(): void
    {
        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desa = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $desa->id,
        ]);
        $user->assignRole('desa-sekretaris');

        $this->createActivity($user, 'desa', $desa->id, 'Aktivitas A');
        $this->createAgendaSurat($user, 'desa', $desa->id, 'A-001');

        $response = $this->actingAs($user)->get(route('dashboard', [
            'section1_month' => '2',
        ]));

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Dashboard')
                ->missing('dashboardStats')
                ->missing('dashboardCharts')
                ->missing('dashboardBlocks')
                ->reloadOnly(['dashboardContext', 'dashboardBlocks'], function (AssertableInertia $reload): void {
                    $reload
                        ->where('dashboardContext.section1_month', '2')
                        ->has('dashboardBlocks')
                        ->missing('dashboardStats')
                        ->missing('dashboardCharts');
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
        $user->assignRole('kecamatan-sekretaris');

        $this->createActivity($user, 'kecamatan', $kecamatanA->id, 'Aktivitas Kecamatan A');
        $this->createActivity($user, 'desa', $desaA->id, 'Aktivitas Desa A');
        $this->createActivity($user, 'kecamatan', $kecamatanB->id, 'Aktivitas Kecamatan B');
        $this->createAgendaSurat($user, 'kecamatan', $kecamatanA->id, 'A-001');
        $this->createAgendaSurat($user, 'kecamatan', $kecamatanB->id, 'B-001');
        $this->createDataWarga($user, 'desa', $desaA->id, 'Kepala A');
        $this->createDataWarga($user, 'desa', $desaB->id, 'Kepala B');

        $totalBooks = $this->expectedBooksTotal();
        $expectedRemaining = $totalBooks - 2;

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page) use ($totalBooks, $expectedRemaining): void {
            $page
                ->component('Dashboard')
                ->missing('dashboardStats')
                ->missing('dashboardCharts')
                ->where('dashboardContext.tahun_anggaran', (string) now()->format('Y'))
                ->missing('dashboardBlocks')

            $this->assertDeferredDashboardBlocks($page, function ($blocks): bool {
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
        $user->assignRole('desa-sekretaris');

        $this->createActivity($user, 'kecamatan', $kecamatan->id, 'Aktivitas Kecamatan');
        $this->createActivity($user, 'desa', $desa->id, 'Aktivitas Desa');
        $this->createAgendaSurat($user, 'kecamatan', $kecamatan->id, 'A-001');
        $this->createDataWarga($user, 'desa', $desa->id, 'Kepala A');

        $totalBooks = $this->expectedBooksTotal();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page) use ($totalBooks): void {
            $page
                ->component('Dashboard')
                ->missing('dashboardStats')
                ->missing('dashboardCharts')
                ->where('auth.user.scope', null)
                ->missing('dashboardBlocks')

            $this->assertDeferredDashboardBlocks($page, fn ($blocks): bool => collect($blocks)->isEmpty());
        });
    }

    public function test_dashboard_desa_sekretaris_menghasilkan_section_1_dan_2_sesuai_kontrak_filter(): void
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
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Dashboard')
                ->missing('dashboardStats')
                ->missing('dashboardCharts')
                ->missing('dashboardBlocks');

            $this->assertDeferredDashboardBlocks($page, function ($blocks): bool {
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
                $section4 = $collected
                    ->where('section.key', 'sekretaris-section-4')
                    ->first();

                return is_array($section1)
                    && is_array($section2)
                    && ($section2['section']['filter']['query_key'] ?? null) === 'section2_group'
                    && ($section2['section']['source_level'] ?? null) === 'desa'
                    && ($section2['sources']['filter_context']['mode'] ?? null) === 'by-level'
                    && ($section2['sources']['filter_context']['level'] ?? null) === 'desa'
                    && $section4 === null
                    && ($section1['section']['source_level'] ?? null) === 'desa'
                    && collect($collected->where('section.key', 'sekretaris-section-2')->pluck('group')->all())->sort()->values()->all() === [
                        'pokja-i',
                        'pokja-ii',
                        'pokja-iii',
                        'pokja-iv',
                    ];
            });
        });
    }

    public function test_dashboard_kecamatan_sekretaris_hanya_menghasilkan_section_1_dan_2_dan_filter_context_sinkron_dengan_query(): void
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

        $totalBooks = $this->expectedBooksTotal();

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page) use ($totalBooks): void {
            $page
                ->component('Dashboard')
                ->missing('dashboardStats')
                ->missing('dashboardCharts')
                ->missing('dashboardBlocks');

            $this->assertDeferredDashboardBlocks($page, function ($blocks) use ($totalBooks): bool {
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
                    && $section3 === null
                    && $section4 === null
                    && ($section1['section']['source_level'] ?? null) === 'kecamatan'
                    && ($section1['charts']['by_desa']['labels'] ?? null) === ['Gombong']
                    && ($section1['charts']['by_desa']['values'] ?? null) === [1]
                    && ($section1['charts']['by_desa']['books_total'] ?? null) === [$totalBooks]
                    && ($section1['charts']['by_desa']['books_filled'] ?? null) === [1]
                    && ($section2['group'] ?? null) === 'pokja-i'
                    && ($section2['section']['filter']['query_key'] ?? null) === 'section2_group'
                    && ($section2['sources']['filter_context']['section2_group'] ?? null) === 'pokja-i'
                    && ($section2['sources']['filter_context']['tahun_anggaran'] ?? null) === now()->format('Y')
                    && ($section2['sources']['filter_context']['level'] ?? null) === 'kecamatan'
                    && ($section2['sources']['filter_context']['section3_group'] ?? null) === 'all';
            });
        });
    }

    private function expectedBooksTotal(): int
    {
        return count(app(DashboardDocumentCoverageRepositoryInterface::class)->trackedModuleSlugs());
    }

    public function test_dashboard_coverage_dokumen_tidak_bocor_ke_tahun_anggaran_lain_pada_area_yang_sama(): void
    {
        $activeBudgetYear = 2026;
        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desa = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $desa->id,
            'active_budget_year' => $activeBudgetYear,
        ]);
        $user->assignRole('desa-sekretaris');

        $this->createActivity($user, 'desa', $desa->id, 'Aktivitas 2026', $activeBudgetYear);
        $this->createActivity($user, 'desa', $desa->id, 'Aktivitas 2025', $activeBudgetYear - 1);
        $this->createAgendaSurat($user, 'desa', $desa->id, 'A-2026', $activeBudgetYear);
        $this->createAgendaSurat($user, 'desa', $desa->id, 'A-2025', $activeBudgetYear - 1);
        $this->createDataWarga($user, 'desa', $desa->id, 'Kepala 2026', $activeBudgetYear);
        $this->createDataWarga($user, 'desa', $desa->id, 'Kepala 2025', $activeBudgetYear - 1);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page) use ($activeBudgetYear) {
            $page
                ->component('Dashboard')
                ->missing('dashboardStats')
                ->missing('dashboardCharts')
                ->where('dashboardContext.tahun_anggaran', (string) $activeBudgetYear)
        });
    }

    public function test_dashboard_kecamatan_sekretaris_mengabaikan_section3_group_dan_tetap_hanya_section_1_dan_2(): void
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
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Dashboard')
                ->missing('dashboardStats')
                ->missing('dashboardCharts')
                ->missing('dashboardBlocks');

            $this->assertDeferredDashboardBlocks($page, function ($blocks): bool {
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
                    && $section3 === null
                    && $section4 === null
                    && ($section2['sources']['filter_context']['section3_group'] ?? null) === 'all';
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
        $pokjaLainUser = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $desa->id,
        ]);
        $pokjaLainUser->assignRole('desa-pokja-ii');
        $this->createActivity($pokjaLainUser, 'desa', $desa->id, 'Aktivitas Pokja II');
        $this->createDataWarga($user, 'desa', $desa->id, 'Kepala Pokja I');

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Dashboard')
                ->missing('dashboardStats')
                ->missing('dashboardCharts')
                ->missing('dashboardBlocks');

            $this->assertDeferredDashboardBlocks($page, function ($blocks): bool {
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
                    && ($block['kind'] ?? null) === 'activity'
                    && ($block['stats']['total'] ?? null) === 1
                    && ($block['section'] ?? null) === null
                    && ($block['sources']['source_group'] ?? null) === 'pokja-i'
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
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Dashboard')
                ->missing('dashboardStats')
                ->missing('dashboardCharts')
                ->missing('dashboardBlocks');

            $this->assertDeferredDashboardBlocks($page, function ($blocks): bool {
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
                    && ($block['sources']['filter_context']['mode'] ?? null) === 'by-level'
                    && ($block['sources']['filter_context']['level'] ?? null) === 'desa'
                    && ($block['sources']['filter_context']['sub_level'] ?? null) === 'all'
                    && in_array('Gombong', $labels, true)
                    && in_array('Bandung', $labels, true)
                    && ! in_array('Sidomukti', $labels, true)
                    && ($totalsByLabel['Gombong'] ?? null) === 1
                    && ($totalsByLabel['Bandung'] ?? null) === 2;
            });
        });
    }

    public function test_dashboard_role_desa_pokja_iv_menggunakan_blok_activity_bulanan_pokja_sendiri(): void
    {
        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desa = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $desa->id,
        ]);
        $user->assignRole('desa-pokja-iv');

        $this->createActivity($user, 'desa', $desa->id, 'Aktivitas Pokja IV');

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Dashboard')
                ->missing('dashboardStats')
                ->missing('dashboardCharts')
                ->missing('dashboardBlocks');

            $this->assertDeferredDashboardBlocks($page, function ($blocks): bool {
                $block = collect($blocks)->first();
                if (! is_array($block)) {
                    return false;
                }

                $monthlyValues = $block['charts']['monthly']['values'] ?? [];

                return ($block['group'] ?? null) === 'pokja-iv'
                    && ($block['kind'] ?? null) === 'activity'
                    && ($block['sources']['source_group'] ?? null) === 'pokja-iv'
                    && ($block['section'] ?? null) === null
                    && is_array($monthlyValues)
                    && array_sum($monthlyValues) === 1;
            });
        });
    }

    public function test_dashboard_role_kecamatan_pokja_mengunci_filter_context_ke_level_desa_untuk_breakdown_per_desa(): void
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
        $levelResponse->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Dashboard')
                ->missing('dashboardStats')
                ->missing('dashboardCharts')
                ->missing('dashboardBlocks');

            $this->assertDeferredDashboardBlocks($page, function ($blocks): bool {
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
        $subLevelResponse->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Dashboard')
                ->missing('dashboardStats')
                ->missing('dashboardCharts')
                ->missing('dashboardBlocks');

            $this->assertDeferredDashboardBlocks($page, function ($blocks): bool {
                $first = collect($blocks)->first();

                return is_array($first)
                    && ($first['sources']['filter_context']['mode'] ?? null) === 'by-level'
                    && ($first['sources']['filter_context']['level'] ?? null) === 'desa'
                    && ($first['sources']['filter_context']['sub_level'] ?? null) === 'all';
            });
        });
    }

    private function assertDeferredDashboardBlocks(AssertableInertia $page, callable $callback): void
    {
        $page->loadDeferredProps('dashboard-blocks', function (AssertableInertia $reload) use ($callback): void {
            $reload
                ->has('dashboardBlocks')
                ->missing('dashboardStats')
                ->missing('dashboardCharts')
                ->missing('dashboardContext')
                ->where('dashboardBlocks', $callback);
        });
    }

    private function createActivity(User $user, string $level, int $areaId, string $title, ?int $tahunAnggaran = null): void
    {
        Activity::create([
            'title' => $title,
            'level' => $level,
            'area_id' => $areaId,
            'created_by' => $user->id,
            'activity_date' => sprintf('%d-01-15', $tahunAnggaran ?? (int) now()->format('Y')),
            'status' => 'published',
            'tahun_anggaran' => $tahunAnggaran ?? (int) ($user->active_budget_year ?? now()->format('Y')),
        ]);
    }

    private function createAgendaSurat(User $user, string $level, int $areaId, string $nomorSurat, ?int $tahunAnggaran = null): void
    {
        AgendaSurat::create([
            'jenis_surat' => 'masuk',
            'tanggal_terima' => sprintf('%d-01-15', $tahunAnggaran ?? (int) now()->format('Y')),
            'tanggal_surat' => sprintf('%d-01-15', $tahunAnggaran ?? (int) now()->format('Y')),
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
            'tahun_anggaran' => $tahunAnggaran ?? (int) ($user->active_budget_year ?? now()->format('Y')),
        ]);
    }

    private function createDataWarga(User $user, string $level, int $areaId, string $kepalaKeluarga, ?int $tahunAnggaran = null): void
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
            'tahun_anggaran' => $tahunAnggaran ?? (int) ($user->active_budget_year ?? now()->format('Y')),
        ]);
    }
}
