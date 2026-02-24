<?php

namespace App\Domains\Wilayah\Dashboard\UseCases;

use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Domains\Wilayah\Dashboard\Repositories\DashboardDocumentCoverageRepositoryInterface;
use App\Domains\Wilayah\Dashboard\Repositories\DashboardGroupCoverageRepositoryInterface;
use App\Domains\Wilayah\Services\RoleMenuVisibilityService;
use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Models\User;
use Illuminate\Support\Collection;

class BuildRoleAwareDashboardBlocksUseCase
{
    /**
     * @var list<string>
     */
    private const POKJA_GROUPS = [
        'pokja-i',
        'pokja-ii',
        'pokja-iii',
        'pokja-iv',
    ];

    private const SECTION_SEKRETARIS_1 = 'sekretaris-section-1';
    private const SECTION_SEKRETARIS_2 = 'sekretaris-section-2';
    private const SECTION_SEKRETARIS_3 = 'sekretaris-section-3';
    private const SECTION_SEKRETARIS_4 = 'sekretaris-section-4';

    /**
     * @var array<string, string>
     */
    private const GROUP_LABELS = [
        'sekretaris-tpk' => 'Sekretaris TPK',
        'pokja-i' => 'Pokja I',
        'pokja-ii' => 'Pokja II',
        'pokja-iii' => 'Pokja III',
        'pokja-iv' => 'Pokja IV',
        'monitoring' => 'Monitoring Kecamatan',
    ];

    public function __construct(
        private readonly RoleMenuVisibilityService $roleMenuVisibilityService,
        private readonly UserAreaContextService $userAreaContextService,
        private readonly DashboardDocumentCoverageRepositoryInterface $dashboardDocumentCoverageRepository,
        private readonly DashboardGroupCoverageRepositoryInterface $dashboardGroupCoverageRepository
    ) {
    }

    /**
     * @param array<string, mixed> $activityData
     * @param array<string, mixed> $documentData
     * @param array{mode?: mixed, level?: mixed, sub_level?: mixed, block?: mixed, section1_month?: mixed, section2_group?: mixed, section3_group?: mixed} $dashboardContext
     * @return array<int, array<string, mixed>>
     */
    public function execute(
        User $user,
        array $activityData,
        array $documentData,
        array $dashboardContext = []
    ): array {
        $effectiveScope = $this->userAreaContextService->resolveEffectiveScope($user);
        if (! is_string($effectiveScope)) {
            return [];
        }

        $visibility = $this->roleMenuVisibilityService->resolveForScope($user, $effectiveScope);
        $groupModes = $visibility['groups'] ?? [];
        if (! is_array($groupModes) || $groupModes === []) {
            return [];
        }

        $documentItems = collect($documentData['charts']['coverage_per_buku']['items'] ?? [])
            ->filter(static fn ($item): bool => is_array($item) && is_string($item['slug'] ?? null))
            ->values();

        if ($this->shouldUseSekretarisSections($groupModes)) {
            return $this->buildSekretarisSectionBlocks(
                $user,
                $effectiveScope,
                $groupModes,
                $activityData,
                $documentItems,
                $dashboardContext
            );
        }

        $blocks = [];

        if (array_key_exists('sekretaris-tpk', $groupModes)) {
            $blocks[] = $this->buildActivityBlock(
                $effectiveScope,
                (string) $groupModes['sekretaris-tpk'],
                $activityData,
                $dashboardContext,
                null
            );
        }

        foreach ($groupModes as $groupKey => $mode) {
            $modules = $this->roleMenuVisibilityService->modulesForGroup((string) $groupKey);
            if ($modules === []) {
                continue;
            }

            if (
                $effectiveScope === ScopeLevel::KECAMATAN->value
                && in_array((string) $groupKey, self::POKJA_GROUPS, true)
            ) {
                $kecamatanByDesaBlock = $this->buildKecamatanPokjaByDesaBlock(
                    $user,
                    (string) $groupKey,
                    (string) $mode,
                    $effectiveScope,
                    $modules,
                    $dashboardContext
                );

                if (is_array($kecamatanByDesaBlock)) {
                    $blocks[] = $kecamatanByDesaBlock;
                    continue;
                }
            }

            $groupDocumentItems = $documentItems
                ->filter(
                    static fn (array $item): bool => in_array((string) $item['slug'], $modules, true)
                )
                ->values();

            if ($groupDocumentItems->isEmpty()) {
                continue;
            }

            $blocks[] = $this->buildDocumentBlock(
                (string) $groupKey,
                (string) $mode,
                $effectiveScope,
                $groupDocumentItems,
                $modules,
                $dashboardContext,
                null,
                ''
            );
        }

        return $blocks;
    }

    /**
     * @param array<string, mixed> $activityData
     * @param array{mode?: mixed, level?: mixed, sub_level?: mixed, block?: mixed, section1_month?: mixed, section2_group?: mixed, section3_group?: mixed} $dashboardContext
     * @return array<string, mixed>
     */
    private function buildActivityBlock(
        string $effectiveScope,
        string $mode,
        array $activityData,
        array $dashboardContext,
        ?array $section
    ): array {
        $block = [
            'key' => 'activity-sekretaris-tpk',
            'kind' => 'activity',
            'group' => 'sekretaris-tpk',
            'group_label' => self::GROUP_LABELS['sekretaris-tpk'],
            'mode' => $mode,
            'title' => sprintf(
                'Dashboard %s - %s',
                self::GROUP_LABELS['sekretaris-tpk'],
                strtoupper($effectiveScope)
            ),
            'stats' => $activityData['stats'] ?? [],
            'charts' => $activityData['charts'] ?? [],
            'sources' => [
                'source_group' => 'sekretaris-tpk',
                'source_scope' => $effectiveScope,
                'source_area_type' => $this->resolveSourceAreaType($effectiveScope),
                'source_modules' => ['activities'],
                'source_note' => 'Agregasi aktivitas sesuai scope-area efektif pengguna.',
                'filter_context' => $this->buildFilterContext($dashboardContext),
            ],
        ];

        return $this->attachSection($block, $section);
    }

    /**
     * @param list<string> $modules
     * @param array{mode?: mixed, level?: mixed, sub_level?: mixed, block?: mixed, section1_month?: mixed, section2_group?: mixed, section3_group?: mixed} $dashboardContext
     * @return array<string, mixed>|null
     */
    private function buildKecamatanPokjaByDesaBlock(
        User $user,
        string $groupKey,
        string $mode,
        string $effectiveScope,
        array $modules,
        array $dashboardContext
    ): ?array {
        $desaBreakdown = collect(
            $this->dashboardGroupCoverageRepository->buildBreakdownByDesaForGroup($user, $groupKey)
        )->values();

        if ($desaBreakdown->isEmpty()) {
            return null;
        }

        $coverageItems = $desaBreakdown
            ->map(static function (array $item): array {
                $desaId = (int) ($item['desa_id'] ?? 0);
                $desaName = (string) ($item['desa_name'] ?? '-');
                $total = (int) ($item['total'] ?? 0);

                return [
                    'slug' => sprintf('desa-%d', $desaId),
                    'label' => $desaName,
                    'total' => $total,
                    'resolved_total' => $total,
                    'per_module' => is_array($item['per_module'] ?? null) ? $item['per_module'] : [],
                ];
            })
            ->values();

        $totalDesa = $coverageItems->count();
        $desaTerisi = $coverageItems->filter(static fn (array $item): bool => (int) ($item['total'] ?? 0) > 0)->count();
        $totalEntries = $coverageItems->sum(static fn (array $item): int => (int) ($item['total'] ?? 0));

        return [
            'key' => sprintf('documents-%s-kecamatan-desa-breakdown', $groupKey),
            'kind' => 'documents',
            'group' => $groupKey,
            'group_label' => self::GROUP_LABELS[$groupKey] ?? $groupKey,
            'mode' => $mode,
            'title' => sprintf(
                'Dashboard %s - %s (Per Desa)',
                self::GROUP_LABELS[$groupKey] ?? $groupKey,
                strtoupper($effectiveScope)
            ),
            'stats' => [
                'total_buku_tracked' => $totalDesa,
                'buku_terisi' => $desaTerisi,
                'buku_belum_terisi' => $totalDesa - $desaTerisi,
                'total_entri_buku' => $totalEntries,
            ],
            'charts' => [
                'coverage_per_module' => [
                    'labels' => $coverageItems->pluck('label')->all(),
                    'values' => $coverageItems->pluck('total')->all(),
                    'items' => $coverageItems->all(),
                    'dimension' => 'desa',
                ],
            ],
            'sources' => [
                'source_group' => $groupKey,
                'source_scope' => $effectiveScope,
                'source_area_type' => 'desa-turunan',
                'source_modules' => $modules,
                'tracked_modules' => $modules,
                'source_note' => 'Agregasi pokja tingkat kecamatan dirinci per desa turunan sesuai group aktif.',
                'filter_context' => $this->buildFilterContext($dashboardContext),
            ],
        ];
    }

    /**
     * @param Collection<int, array<string, mixed>> $groupDocumentItems
     * @param list<string> $modules
     * @param array{mode?: mixed, level?: mixed, sub_level?: mixed, block?: mixed, section1_month?: mixed, section2_group?: mixed, section3_group?: mixed} $dashboardContext
     * @return array<string, mixed>
     */
    private function buildDocumentBlock(
        string $groupKey,
        string $mode,
        string $effectiveScope,
        Collection $groupDocumentItems,
        array $modules,
        array $dashboardContext,
        ?array $section,
        string $keySuffix
    ): array {
        $statsAccumulator = $groupDocumentItems->reduce(
            function (array $carry, array $item) use ($dashboardContext): array {
                $resolvedTotal = $this->resolveItemTotalByContext($item, $dashboardContext);
                $carry['total_entri_buku'] += $resolvedTotal;
                $carry['buku_terisi'] += $resolvedTotal > 0 ? 1 : 0;

                return $carry;
            },
            [
                'total_entri_buku' => 0,
                'buku_terisi' => 0,
            ]
        );

        $items = $groupDocumentItems
            ->map(function (array $item) use ($dashboardContext): array {
                $item['resolved_total'] = $this->resolveItemTotalByContext($item, $dashboardContext);

                return $item;
            })
            ->values();

        $resolvedKeySuffix = trim($keySuffix);
        $blockKey = $resolvedKeySuffix === ''
            ? sprintf('documents-%s', $groupKey)
            : sprintf('documents-%s-%s', $groupKey, $resolvedKeySuffix);

        $block = [
            'key' => $blockKey,
            'kind' => 'documents',
            'group' => $groupKey,
            'group_label' => self::GROUP_LABELS[$groupKey] ?? $groupKey,
            'mode' => $mode,
            'title' => sprintf(
                'Dashboard %s - %s',
                self::GROUP_LABELS[$groupKey] ?? $groupKey,
                strtoupper($effectiveScope)
            ),
            'stats' => [
                'total_buku_tracked' => $items->count(),
                'buku_terisi' => (int) $statsAccumulator['buku_terisi'],
                'buku_belum_terisi' => $items->count() - (int) $statsAccumulator['buku_terisi'],
                'total_entri_buku' => (int) $statsAccumulator['total_entri_buku'],
            ],
            'charts' => [
                'coverage_per_module' => [
                    'labels' => $items->map(
                        static fn (array $item): string => (string) ($item['label'] ?? $item['slug'] ?? '-')
                    )->all(),
                    'values' => $items->map(
                        static fn (array $item): int => (int) ($item['resolved_total'] ?? 0)
                    )->all(),
                    'items' => $items->all(),
                ],
            ],
            'sources' => [
                'source_group' => $groupKey,
                'source_scope' => $effectiveScope,
                'source_area_type' => $this->resolveSourceAreaType($effectiveScope),
                'source_modules' => $modules,
                'tracked_modules' => $items->map(
                    static fn (array $item): string => (string) ($item['slug'] ?? '-')
                )->all(),
                'source_note' => 'Agregasi diturunkan dari coverage dokumen per modul sesuai group menu dan scope-area efektif.',
                'filter_context' => $this->buildFilterContext($dashboardContext),
            ],
        ];

        return $this->attachSection($block, $section);
    }

    /**
     * @param array{mode?: mixed, level?: mixed, sub_level?: mixed, block?: mixed, section1_month?: mixed, section2_group?: mixed, section3_group?: mixed} $dashboardContext
     * @return array{mode: string, level: string, sub_level: string, section1_month: string, section2_group: string, section3_group: string}
     */
    private function buildFilterContext(array $dashboardContext): array
    {
        return [
            'mode' => $this->normalizeContextToken($dashboardContext['mode'] ?? null, 'all'),
            'level' => $this->normalizeContextToken($dashboardContext['level'] ?? null, 'all'),
            'sub_level' => $this->normalizeContextToken($dashboardContext['sub_level'] ?? null, 'all'),
            'section1_month' => $this->normalizeContextToken($dashboardContext['section1_month'] ?? null, 'all'),
            'section2_group' => $this->normalizeContextToken($dashboardContext['section2_group'] ?? null, 'all'),
            'section3_group' => $this->normalizeContextToken($dashboardContext['section3_group'] ?? null, 'all'),
        ];
    }

    private function resolveSourceAreaType(string $effectiveScope): string
    {
        return $effectiveScope === ScopeLevel::KECAMATAN->value
            ? 'area-sendiri+desa-turunan'
            : 'area-sendiri';
    }

    /**
     * @param array<string, mixed> $item
     * @param array{mode?: mixed, level?: mixed, sub_level?: mixed, block?: mixed, section1_month?: mixed, section2_group?: mixed, section3_group?: mixed} $dashboardContext
     */
    private function resolveItemTotalByContext(array $item, array $dashboardContext): int
    {
        $mode = $this->normalizeContextToken($dashboardContext['mode'] ?? null, 'all');
        if ($mode !== 'by-level') {
            return (int) ($item['total'] ?? 0);
        }

        $level = $this->normalizeContextToken($dashboardContext['level'] ?? null, 'all');

        return match ($level) {
            ScopeLevel::DESA->value => (int) ($item['desa'] ?? 0),
            ScopeLevel::KECAMATAN->value => (int) ($item['kecamatan'] ?? 0),
            default => (int) ($item['total'] ?? 0),
        };
    }

    private function normalizeContextToken(mixed $value, string $fallback): string
    {
        if (! is_scalar($value)) {
            return $fallback;
        }

        $token = strtolower(trim((string) $value));

        return $token === '' ? $fallback : $token;
    }

    /**
     * @param array<string, string> $groupModes
     */
    private function shouldUseSekretarisSections(array $groupModes): bool
    {
        if (! array_key_exists('sekretaris-tpk', $groupModes)) {
            return false;
        }

        foreach (self::POKJA_GROUPS as $group) {
            if (array_key_exists($group, $groupModes)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array<string, string> $groupModes
     * @param array<string, mixed> $activityData
     * @param Collection<int, array<string, mixed>> $documentItems
     * @param array{mode?: mixed, level?: mixed, sub_level?: mixed, block?: mixed, section1_month?: mixed, section2_group?: mixed, section3_group?: mixed} $dashboardContext
     * @return array<int, array<string, mixed>>
     */
    private function buildSekretarisSectionBlocks(
        User $user,
        string $effectiveScope,
        array $groupModes,
        array $activityData,
        Collection $documentItems,
        array $dashboardContext
    ): array {
        $blocks = [];
        $pokjaGroups = array_values(
            array_filter(
                self::POKJA_GROUPS,
                static fn (string $group): bool => array_key_exists($group, $groupModes)
            )
        );

        $sekretarisMode = (string) ($groupModes['sekretaris-tpk'] ?? RoleMenuVisibilityService::MODE_READ_ONLY);
        $blocks[] = $this->buildActivityBlock(
            $effectiveScope,
            $sekretarisMode,
            $activityData,
            [
                'mode' => 'by-level',
                'level' => $effectiveScope,
                'sub_level' => 'all',
                'section1_month' => $dashboardContext['section1_month'] ?? 'all',
                'section2_group' => $dashboardContext['section2_group'] ?? 'all',
                'section3_group' => $dashboardContext['section3_group'] ?? 'all',
            ],
            $this->buildSectionMeta(self::SECTION_SEKRETARIS_1, $effectiveScope)
        );

        $sameLevelContext = [
            'mode' => 'by-level',
            'level' => $effectiveScope,
            'sub_level' => 'all',
            'section1_month' => $dashboardContext['section1_month'] ?? 'all',
            'section2_group' => $dashboardContext['section2_group'] ?? 'all',
            'section3_group' => $dashboardContext['section3_group'] ?? 'all',
        ];

        foreach ($pokjaGroups as $groupKey) {
            $modules = $this->roleMenuVisibilityService->modulesForGroup($groupKey);
            if ($modules === []) {
                continue;
            }

            $groupDocumentItems = $documentItems
                ->filter(
                    static fn (array $item): bool => in_array((string) $item['slug'], $modules, true)
                )
                ->values();

            if ($groupDocumentItems->isEmpty()) {
                continue;
            }

            $mode = (string) ($groupModes[$groupKey] ?? RoleMenuVisibilityService::MODE_READ_ONLY);
            $blocks[] = $this->buildDocumentBlock(
                $groupKey,
                $mode,
                $effectiveScope,
                $groupDocumentItems,
                $modules,
                $sameLevelContext,
                $this->buildSectionMeta(self::SECTION_SEKRETARIS_2, $effectiveScope),
                'same-level'
            );
        }

        if ($effectiveScope !== ScopeLevel::KECAMATAN->value) {
            return $blocks;
        }

        $lowerLevelContext = [
            'mode' => 'by-level',
            'level' => ScopeLevel::DESA->value,
            'sub_level' => 'all',
            'section1_month' => $dashboardContext['section1_month'] ?? 'all',
            'section2_group' => $dashboardContext['section2_group'] ?? 'all',
            'section3_group' => $dashboardContext['section3_group'] ?? 'all',
        ];

        foreach ($pokjaGroups as $groupKey) {
            $modules = $this->roleMenuVisibilityService->modulesForGroup($groupKey);
            if ($modules === []) {
                continue;
            }

            $groupDocumentItems = $documentItems
                ->filter(
                    static fn (array $item): bool => in_array((string) $item['slug'], $modules, true)
                )
                ->values();

            if ($groupDocumentItems->isEmpty()) {
                continue;
            }

            $mode = (string) ($groupModes[$groupKey] ?? RoleMenuVisibilityService::MODE_READ_ONLY);
            $blocks[] = $this->buildDocumentBlock(
                $groupKey,
                $mode,
                $effectiveScope,
                $groupDocumentItems,
                $modules,
                $lowerLevelContext,
                $this->buildSectionMeta(self::SECTION_SEKRETARIS_3, $effectiveScope),
                'lower-level'
            );
        }

        if ($this->shouldRenderSekretarisSection4($effectiveScope, $pokjaGroups, $dashboardContext)) {
            $section4Block = $this->buildSekretarisSection4Block(
                $user,
                $effectiveScope,
                (string) ($groupModes['pokja-i'] ?? RoleMenuVisibilityService::MODE_READ_ONLY),
                $dashboardContext
            );

            if (is_array($section4Block)) {
                $blocks[] = $section4Block;
            }
        }

        return $blocks;
    }

    /**
     * @param list<string> $pokjaGroups
     * @param array{mode?: mixed, level?: mixed, sub_level?: mixed, block?: mixed, section1_month?: mixed, section2_group?: mixed, section3_group?: mixed} $dashboardContext
     */
    private function shouldRenderSekretarisSection4(
        string $effectiveScope,
        array $pokjaGroups,
        array $dashboardContext
    ): bool {
        if ($effectiveScope !== ScopeLevel::KECAMATAN->value) {
            return false;
        }

        if (! in_array('pokja-i', $pokjaGroups, true)) {
            return false;
        }

        $selectedSection3Group = $this->normalizeContextToken($dashboardContext['section3_group'] ?? null, 'all');

        return $selectedSection3Group === 'pokja-i';
    }

    /**
     * @param array{mode?: mixed, level?: mixed, sub_level?: mixed, block?: mixed, section1_month?: mixed, section2_group?: mixed, section3_group?: mixed} $dashboardContext
     * @return array<string, mixed>|null
     */
    private function buildSekretarisSection4Block(
        User $user,
        string $effectiveScope,
        string $mode,
        array $dashboardContext
    ): ?array {
        $sourceModules = [
            'data-warga',
            'data-kegiatan-warga',
            'bkl',
            'bkr',
            'paar',
        ];

        $desaBreakdown = collect(
            $this->dashboardGroupCoverageRepository->buildBreakdownByDesaForModules($user, $sourceModules)
        )->values();

        $coverageItems = $desaBreakdown
            ->map(static function (array $item): array {
                $desaId = (int) ($item['desa_id'] ?? 0);
                $desaName = (string) ($item['desa_name'] ?? '-');
                $total = (int) ($item['total'] ?? 0);

                return [
                    'slug' => sprintf('desa-%d', $desaId),
                    'label' => $desaName,
                    'total' => $total,
                    'resolved_total' => $total,
                    'per_module' => is_array($item['per_module'] ?? null) ? $item['per_module'] : [],
                ];
            })
            ->values();

        $totalDesa = $coverageItems->count();
        $desaTerisi = $coverageItems->filter(static fn (array $item): bool => (int) ($item['total'] ?? 0) > 0)->count();
        $totalEntries = $coverageItems->sum(static fn (array $item): int => (int) ($item['total'] ?? 0));

        return $this->attachSection([
            'key' => 'documents-pokja-i-desa-breakdown',
            'kind' => 'documents',
            'group' => 'pokja-i',
            'group_label' => self::GROUP_LABELS['pokja-i'],
            'mode' => $mode,
            'title' => sprintf(
                'Dashboard %s - %s (Rincian Per Desa)',
                self::GROUP_LABELS['pokja-i'],
                strtoupper($effectiveScope)
            ),
            'stats' => [
                'total_buku_tracked' => $totalDesa,
                'buku_terisi' => $desaTerisi,
                'buku_belum_terisi' => $totalDesa - $desaTerisi,
                'total_entri_buku' => $totalEntries,
            ],
            'charts' => [
                'coverage_per_module' => [
                    'labels' => $coverageItems->pluck('label')->all(),
                    'values' => $coverageItems->pluck('total')->all(),
                    'items' => $coverageItems->all(),
                    'dimension' => 'desa',
                ],
            ],
            'sources' => [
                'source_group' => 'pokja-i',
                'source_scope' => $effectiveScope,
                'source_area_type' => 'desa-turunan',
                'source_modules' => $sourceModules,
                'tracked_modules' => $sourceModules,
                'source_note' => 'Rincian sumber data Pokja I per desa turunan pada kecamatan aktif.',
                'filter_context' => $this->buildFilterContext([
                    'mode' => 'by-level',
                    'level' => ScopeLevel::DESA->value,
                    'sub_level' => 'all',
                    'section1_month' => $dashboardContext['section1_month'] ?? 'all',
                    'section2_group' => $dashboardContext['section2_group'] ?? 'all',
                    'section3_group' => $dashboardContext['section3_group'] ?? 'all',
                ]),
            ],
        ], $this->buildSectionMeta(self::SECTION_SEKRETARIS_4, $effectiveScope));
    }

    /**
     * @return array<string, mixed>
     */
    private function buildSectionMeta(string $sectionKey, string $effectiveScope): array
    {
        $groupOptions = [
            ['value' => 'all', 'label' => 'All'],
            ['value' => 'pokja-i', 'label' => 'Pokja I'],
            ['value' => 'pokja-ii', 'label' => 'Pokja II'],
            ['value' => 'pokja-iii', 'label' => 'Pokja III'],
            ['value' => 'pokja-iv', 'label' => 'Pokja IV'],
        ];

        return match ($sectionKey) {
            self::SECTION_SEKRETARIS_1 => [
                'key' => self::SECTION_SEKRETARIS_1,
                'label' => 'Section 1 - Domain Sekretaris',
                'filter' => null,
                'source_level' => $effectiveScope,
            ],
            self::SECTION_SEKRETARIS_2 => [
                'key' => self::SECTION_SEKRETARIS_2,
                'label' => 'Section 2 - Pokja Level Aktif',
                'filter' => [
                    'query_key' => 'section2_group',
                    'default' => 'all',
                    'options' => $groupOptions,
                ],
                'source_level' => $effectiveScope,
            ],
            self::SECTION_SEKRETARIS_3 => [
                'key' => self::SECTION_SEKRETARIS_3,
                'label' => 'Section 3 - Pokja Level Bawah',
                'filter' => [
                    'query_key' => 'section3_group',
                    'default' => 'all',
                    'options' => $groupOptions,
                ],
                'source_level' => ScopeLevel::DESA->value,
            ],
            self::SECTION_SEKRETARIS_4 => [
                'key' => self::SECTION_SEKRETARIS_4,
                'label' => 'Section 4 - Rincian Pokja I per Desa',
                'filter' => null,
                'depends_on' => 'section3_group:pokja-i',
                'source_level' => ScopeLevel::DESA->value,
            ],
            default => [
                'key' => $sectionKey,
                'label' => $sectionKey,
                'filter' => null,
                'source_level' => $effectiveScope,
            ],
        };
    }

    /**
     * @param array<string, mixed> $block
     * @param array<string, mixed>|null $section
     * @return array<string, mixed>
     */
    private function attachSection(array $block, ?array $section): array
    {
        if (! is_array($section)) {
            return $block;
        }

        $block['section'] = $section;

        return $block;
    }
}
