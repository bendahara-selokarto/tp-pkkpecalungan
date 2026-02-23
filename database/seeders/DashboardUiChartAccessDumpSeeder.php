<?php

namespace Database\Seeders;

use App\Domains\Wilayah\Models\Area;
use App\Domains\Wilayah\Dashboard\UseCases\BuildDashboardDocumentCoverageUseCase;
use App\Domains\Wilayah\Dashboard\UseCases\BuildRoleAwareDashboardBlocksUseCase;
use App\Domains\Wilayah\Services\RoleMenuVisibilityService;
use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Models\User;
use App\Services\DashboardActivityChartService;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class DashboardUiChartAccessDumpSeeder extends Seeder
{
    private const TARGET_KECAMATAN = 'Pecalungan';
    private const DEFAULT_PASSWORD = 'password123';

    /**
     * @var list<string>
     */
    private const ROLE_SET = [
        'desa-sekretaris',
        'desa-pokja-i',
        'desa-pokja-ii',
        'desa-pokja-iii',
        'desa-pokja-iv',
        'kecamatan-sekretaris',
        'kecamatan-pokja-i',
        'kecamatan-pokja-ii',
        'kecamatan-pokja-iii',
        'kecamatan-pokja-iv',
    ];

    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            WilayahSeeder::class,
        ]);

        $kecamatanArea = Area::query()
            ->where('level', 'kecamatan')
            ->where('name', self::TARGET_KECAMATAN)
            ->first();

        if (! $kecamatanArea) {
            $this->command?->warn('DashboardUiChartAccessDumpSeeder dibatalkan: kecamatan target tidak ditemukan.');

            return;
        }

        if (! $this->hasExistingDashboardDataset((int) $kecamatanArea->id)) {
            $this->call([DashboardNaturalBatangSeeder::class]);
        }

        $desaArea = Area::query()
            ->where('level', 'desa')
            ->where('parent_id', $kecamatanArea->id)
            ->orderBy('id')
            ->first();

        if (! $desaArea) {
            $this->command?->warn('DashboardUiChartAccessDumpSeeder dibatalkan: desa turunan tidak ditemukan.');

            return;
        }

        $desaAreas = Area::query()
            ->where('level', 'desa')
            ->where('parent_id', $kecamatanArea->id)
            ->orderBy('id')
            ->get();

        $roleMenuVisibilityService = app(RoleMenuVisibilityService::class);
        $userAreaContextService = app(UserAreaContextService::class);
        $dashboardActivityChartService = app(DashboardActivityChartService::class);
        $buildDashboardDocumentCoverageUseCase = app(BuildDashboardDocumentCoverageUseCase::class);
        $buildRoleAwareDashboardBlocksUseCase = app(BuildRoleAwareDashboardBlocksUseCase::class);

        $accounts = [];
        foreach (self::ROLE_SET as $roleName) {
            $scope = str_starts_with($roleName, 'kecamatan-') ? 'kecamatan' : 'desa';
            $targetArea = $scope === 'kecamatan' ? $kecamatanArea : $desaArea;

            $user = $this->upsertRoleAccount(
                roleName: $roleName,
                scope: $scope,
                area: $targetArea,
            );

            $accounts[] = $this->buildAccountSnapshot(
                user: $user,
                roleName: $roleName,
                scope: $scope,
                area: $targetArea,
                kecamatanArea: $kecamatanArea,
                desaAreas: $desaAreas,
                roleMenuVisibilityService: $roleMenuVisibilityService,
                userAreaContextService: $userAreaContextService,
                dashboardActivityChartService: $dashboardActivityChartService,
                buildDashboardDocumentCoverageUseCase: $buildDashboardDocumentCoverageUseCase,
                buildRoleAwareDashboardBlocksUseCase: $buildRoleAwareDashboardBlocksUseCase,
            );
        }

        $payload = [
            'generated_at' => now()->toDateTimeString(),
            'dashboard_url' => url('/dashboard'),
            'default_password' => self::DEFAULT_PASSWORD,
            'scope_reference' => [
                'kecamatan' => [
                    'id' => (int) $kecamatanArea->id,
                    'name' => (string) $kecamatanArea->name,
                ],
                'desa' => [
                    'id' => (int) $desaArea->id,
                    'name' => (string) $desaArea->name,
                ],
            ],
            'accounts' => $accounts,
        ];

        $targetPath = 'dumps/dashboard_ui_chart_access_dump.json';
        Storage::disk('local')->put(
            $targetPath,
            json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );

        $absolutePath = Storage::disk('local')->path($targetPath);

        $this->command?->info('DashboardUiChartAccessDumpSeeder selesai.');
        $this->command?->line('Dump file: '.$absolutePath);
    }

    private function hasExistingDashboardDataset(int $kecamatanAreaId): bool
    {
        $desaIds = Area::query()
            ->where('level', 'desa')
            ->where('parent_id', $kecamatanAreaId)
            ->pluck('id')
            ->map(static fn ($id): int => (int) $id)
            ->all();

        $areaIds = array_values(array_unique([
            $kecamatanAreaId,
            ...$desaIds,
        ]));

        return DB::table('activities')
            ->whereIn('area_id', $areaIds)
            ->exists();
    }

    private function upsertRoleAccount(string $roleName, string $scope, Area $area): User
    {
        Role::firstOrCreate(['name' => $roleName]);

        $email = sprintf('ui.chart.%s@dummy.pkk.local', $roleName);
        $name = sprintf('UI Chart %s', Str::of($roleName)->replace('-', ' ')->title());

        $user = User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make(self::DEFAULT_PASSWORD),
                'email_verified_at' => now(),
                'scope' => $scope,
                'area_id' => (int) $area->id,
            ]
        );

        $user->syncRoles([$roleName]);

        return $user;
    }

    /**
     * @param EloquentCollection<int, Area> $desaAreas
     * @return array<string, mixed>
     */
    private function buildAccountSnapshot(
        User $user,
        string $roleName,
        string $scope,
        Area $area,
        Area $kecamatanArea,
        EloquentCollection $desaAreas,
        RoleMenuVisibilityService $roleMenuVisibilityService,
        UserAreaContextService $userAreaContextService,
        DashboardActivityChartService $dashboardActivityChartService,
        BuildDashboardDocumentCoverageUseCase $buildDashboardDocumentCoverageUseCase,
        BuildRoleAwareDashboardBlocksUseCase $buildRoleAwareDashboardBlocksUseCase
    ): array {
        $effectiveScope = $userAreaContextService->resolveEffectiveScope($user) ?? $scope;
        $visibility = $roleMenuVisibilityService->resolveForScope($user, $effectiveScope);
        $subLevelOptions = $this->buildSubLevelOptions($scope, $area, $kecamatanArea, $desaAreas);
        $filterOptions = $this->buildFilterOptions($scope, $subLevelOptions);
        $filterContexts = $this->buildFilterContexts(
            $filterOptions['allowed_levels'],
            $filterOptions['sub_level_options']
        );

        $activityData = $dashboardActivityChartService->buildForUser($user);
        $chartSnapshots = [];
        foreach ($filterContexts as $contextKey => $context) {
            $documentData = $buildDashboardDocumentCoverageUseCase->execute($user, $context);
            $dashboardBlocks = $buildRoleAwareDashboardBlocksUseCase->execute(
                $user,
                $activityData,
                $documentData,
                $context
            );

            $chartSnapshots[$contextKey] = [
                'context' => $context,
                'stats' => [
                    'activity' => $activityData['stats'] ?? [],
                    'documents' => $documentData['stats'] ?? [],
                ],
                'charts' => [
                    'activity' => $activityData['charts'] ?? [],
                    'documents' => $documentData['charts'] ?? [],
                ],
                'blocks' => $dashboardBlocks,
            ];
        }

        $defaultSubLevel = $filterOptions['sub_level_options'][1]['token']
            ?? $filterOptions['sub_level_options'][0]['token']
            ?? 'all';
        $dashboardUrls = [
            'all' => '/dashboard?mode=all&level=all&sub_level=all',
            'by_level_desa' => '/dashboard?mode=by-level&level=desa&sub_level=all',
            'by_sub_level' => '/dashboard?mode=by-sub-level&level=all&sub_level='.$defaultSubLevel,
        ];
        if (in_array('kecamatan', $filterOptions['allowed_levels'], true)) {
            $dashboardUrls['by_level_kecamatan'] = '/dashboard?mode=by-level&level=kecamatan&sub_level=all';
        }

        return [
            'name' => (string) $user->name,
            'email' => (string) $user->email,
            'role' => $roleName,
            'scope' => $effectiveScope,
            'area' => [
                'id' => (int) $area->id,
                'name' => (string) $area->name,
                'level' => (string) $area->level,
            ],
            'visibility' => [
                'group_modes' => $visibility['groups'] ?? [],
                'module_modes' => $visibility['modules'] ?? [],
            ],
            'filter_options' => $filterOptions,
            'dashboard_urls' => $dashboardUrls,
            'chart_snapshots' => $chartSnapshots,
        ];
    }

    /**
     * @param EloquentCollection<int, Area> $desaAreas
     * @return list<array{token: string, label: string, source_level: string, source_area_id: int}>
     */
    private function buildSubLevelOptions(
        string $scope,
        Area $area,
        Area $kecamatanArea,
        EloquentCollection $desaAreas
    ): array {
        $options = [[
            'token' => 'all',
            'label' => 'Semua Sub-Level',
            'source_level' => 'all',
            'source_area_id' => 0,
        ]];

        if ($scope === 'kecamatan') {
            $options[] = [
                'token' => $this->buildAreaToken($kecamatanArea),
                'label' => sprintf('Kecamatan: %s', $kecamatanArea->name),
                'source_level' => (string) $kecamatanArea->level,
                'source_area_id' => (int) $kecamatanArea->id,
            ];

            foreach ($desaAreas as $desa) {
                $options[] = [
                    'token' => $this->buildAreaToken($desa),
                    'label' => sprintf('Desa: %s', $desa->name),
                    'source_level' => (string) $desa->level,
                    'source_area_id' => (int) $desa->id,
                ];
            }

            return $options;
        }

        $options[] = [
            'token' => $this->buildAreaToken($area),
            'label' => sprintf('Desa: %s', $area->name),
            'source_level' => (string) $area->level,
            'source_area_id' => (int) $area->id,
        ];

        return $options;
    }

    /**
     * @param list<array{token: string, label: string, source_level: string, source_area_id: int}> $subLevelOptions
     * @return array{
     *   allowed_modes: list<string>,
     *   allowed_levels: list<string>,
     *   sub_level_options: list<array{token: string, label: string, source_level: string, source_area_id: int}>
     * }
     */
    private function buildFilterOptions(string $scope, array $subLevelOptions): array
    {
        return [
            'allowed_modes' => ['all', 'by-level', 'by-sub-level'],
            'allowed_levels' => $scope === 'kecamatan'
                ? ['all', 'desa', 'kecamatan']
                : ['all', 'desa'],
            'sub_level_options' => $subLevelOptions,
        ];
    }

    /**
     * @param list<string> $allowedLevels
     * @param list<array{token: string, label: string, source_level: string, source_area_id: int}> $subLevelOptions
     * @return array<string, array{mode: string, level: string, sub_level: string}>
     */
    private function buildFilterContexts(array $allowedLevels, array $subLevelOptions): array
    {
        $firstSubLevelToken = 'all';
        foreach ($subLevelOptions as $option) {
            if ($option['token'] !== 'all') {
                $firstSubLevelToken = $option['token'];
                break;
            }
        }

        $contexts = [
            'all' => [
                'mode' => 'all',
                'level' => 'all',
                'sub_level' => 'all',
            ],
            'by_level_desa' => [
                'mode' => 'by-level',
                'level' => 'desa',
                'sub_level' => 'all',
            ],
            'by_sub_level' => [
                'mode' => 'by-sub-level',
                'level' => 'all',
                'sub_level' => $firstSubLevelToken,
            ],
        ];

        if (in_array('kecamatan', $allowedLevels, true)) {
            $contexts['by_level_kecamatan'] = [
                'mode' => 'by-level',
                'level' => 'kecamatan',
                'sub_level' => 'all',
            ];
        }

        return $contexts;
    }

    private function buildAreaToken(Area $area): string
    {
        return sprintf(
            '%s-%d-%s',
            (string) $area->level,
            (int) $area->id,
            Str::slug((string) $area->name, '-')
        );
    }
}
