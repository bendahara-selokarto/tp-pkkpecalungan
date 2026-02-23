<?php

namespace App\Domains\Wilayah\Dashboard\UseCases;

use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Domains\Wilayah\Services\RoleMenuVisibilityService;
use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Models\User;
use Illuminate\Support\Collection;

class BuildRoleAwareDashboardBlocksUseCase
{
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
        private readonly UserAreaContextService $userAreaContextService
    ) {
    }

    /**
     * @param array<string, mixed> $activityData
     * @param array<string, mixed> $documentData
     * @param array{mode?: mixed, level?: mixed, sub_level?: mixed, block?: mixed} $dashboardContext
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

        $blocks = [];

        if (array_key_exists('sekretaris-tpk', $groupModes)) {
            $blocks[] = $this->buildActivityBlock(
                $effectiveScope,
                (string) $groupModes['sekretaris-tpk'],
                $activityData,
                $dashboardContext
            );
        }

        foreach ($groupModes as $groupKey => $mode) {
            $modules = $this->roleMenuVisibilityService->modulesForGroup((string) $groupKey);
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

            $blocks[] = $this->buildDocumentBlock(
                (string) $groupKey,
                (string) $mode,
                $effectiveScope,
                $groupDocumentItems,
                $modules,
                $dashboardContext
            );
        }

        return $blocks;
    }

    /**
     * @param array<string, mixed> $activityData
     * @param array{mode?: mixed, level?: mixed, sub_level?: mixed, block?: mixed} $dashboardContext
     * @return array<string, mixed>
     */
    private function buildActivityBlock(
        string $effectiveScope,
        string $mode,
        array $activityData,
        array $dashboardContext
    ): array {
        return [
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
    }

    /**
     * @param Collection<int, array<string, mixed>> $groupDocumentItems
     * @param list<string> $modules
     * @param array{mode?: mixed, level?: mixed, sub_level?: mixed, block?: mixed} $dashboardContext
     * @return array<string, mixed>
     */
    private function buildDocumentBlock(
        string $groupKey,
        string $mode,
        string $effectiveScope,
        Collection $groupDocumentItems,
        array $modules,
        array $dashboardContext
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

        return [
            'key' => sprintf('documents-%s', $groupKey),
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
    }

    /**
     * @param array{mode?: mixed, level?: mixed, sub_level?: mixed, block?: mixed} $dashboardContext
     * @return array{mode: string, level: string, sub_level: string}
     */
    private function buildFilterContext(array $dashboardContext): array
    {
        return [
            'mode' => $this->normalizeContextToken($dashboardContext['mode'] ?? null, 'all'),
            'level' => $this->normalizeContextToken($dashboardContext['level'] ?? null, 'all'),
            'sub_level' => $this->normalizeContextToken($dashboardContext['sub_level'] ?? null, 'all'),
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
     * @param array{mode?: mixed, level?: mixed, sub_level?: mixed, block?: mixed} $dashboardContext
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
}
