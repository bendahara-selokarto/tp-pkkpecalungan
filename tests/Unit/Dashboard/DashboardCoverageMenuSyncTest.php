<?php

namespace Tests\Unit\Dashboard;

use App\Domains\Wilayah\Dashboard\Repositories\DashboardDocumentCoverageRepositoryInterface;
use App\Domains\Wilayah\Services\RoleMenuVisibilityService;
use Tests\TestCase;

class DashboardCoverageMenuSyncTest extends TestCase
{
    /**
     * Slug menu yang saat ini belum masuk coverage dashboard dokumen.
     * Jika ada penambahan slug baru di menu, daftar ini wajib dievaluasi ulang.
     *
     * @var list<string>
     */
    private const EXPLICIT_NON_COVERAGE_MENU_SLUGS = [
        'anggota-tim-penggerak-kader',
        'anggota-pokja',
        'bantuans',
        'bkl',
        'bkr',
        'buku-daftar-hadir',
        'buku-tamu',
        'buku-notulen-rapat',
        'desa-activities',
        'laporan-tahunan-pkk',
        'pilot-project-keluarga-sehat',
        'pilot-project-naskah-pelaporan',
        'paar',
        'prestasi-lomba',
        'program-prioritas',
    ];

    public function test_semua_slug_coverage_dashboard_terpetakan_di_grup_menu(): void
    {
        $trackedCoverageSlugs = collect($this->dashboardCoverageRepository()->trackedModuleSlugs())
            ->unique()
            ->sort()
            ->values();

        $menuSlugs = $this->collectMenuModuleSlugs();

        $missingOnMenu = $trackedCoverageSlugs->diff($menuSlugs)->values()->all();
        $this->assertSame([], $missingOnMenu);
    }

    public function test_slug_menu_wajib_masuk_coverage_atau_didaftarkan_sebagai_non_coverage_eksplisit(): void
    {
        $trackedCoverageSlugs = collect($this->dashboardCoverageRepository()->trackedModuleSlugs())
            ->unique()
            ->values();
        $menuSlugs = $this->collectMenuModuleSlugs();

        $untrackedMenuSlugs = $menuSlugs
            ->diff($trackedCoverageSlugs)
            ->sort()
            ->values()
            ->all();
        $expectedUntrackedMenuSlugs = collect(self::EXPLICIT_NON_COVERAGE_MENU_SLUGS)
            ->sort()
            ->values()
            ->all();

        $this->assertSame($expectedUntrackedMenuSlugs, $untrackedMenuSlugs);
    }

    public function test_group_dashboard_utama_memiliki_minimal_satu_slug_coverage(): void
    {
        $trackedCoverageSlugs = collect($this->dashboardCoverageRepository()->trackedModuleSlugs())
            ->unique()
            ->values();
        $roleMenuVisibilityService = $this->app->make(RoleMenuVisibilityService::class);

        foreach (['sekretaris-tpk', 'pokja-i', 'pokja-ii', 'pokja-iii', 'pokja-iv'] as $group) {
            $groupModules = collect($roleMenuVisibilityService->modulesForGroup($group))->unique()->values();
            $coveredModules = $groupModules->intersect($trackedCoverageSlugs)->values()->all();

            $this->assertNotSame(
                [],
                $coveredModules,
                sprintf(
                    'Group %s harus memiliki minimal satu module yang ikut coverage dashboard.',
                    $group
                )
            );
        }
    }

    private function dashboardCoverageRepository(): DashboardDocumentCoverageRepositoryInterface
    {
        return $this->app->make(DashboardDocumentCoverageRepositoryInterface::class);
    }

    /**
     * @return \Illuminate\Support\Collection<int, string>
     */
    private function collectMenuModuleSlugs()
    {
        $roleMenuVisibilityService = $this->app->make(RoleMenuVisibilityService::class);

        return collect([
            'sekretaris-tpk',
            'pokja-i',
            'pokja-ii',
            'pokja-iii',
            'pokja-iv',
            'monitoring',
        ])->flatMap(
            fn (string $group): array => $roleMenuVisibilityService->modulesForGroup($group)
        )->unique()->sort()->values();
    }
}
