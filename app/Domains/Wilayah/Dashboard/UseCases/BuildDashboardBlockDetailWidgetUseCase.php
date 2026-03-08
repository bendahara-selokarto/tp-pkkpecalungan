<?php

namespace App\Domains\Wilayah\Dashboard\UseCases;

use App\Domains\Wilayah\Dashboard\Repositories\DashboardGroupCoverageRepositoryInterface;
use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Domains\Wilayah\Services\RoleMenuVisibilityService;
use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Models\User;

class BuildDashboardBlockDetailWidgetUseCase
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

    /**
     * @var list<string>
     */
    private const SECTION4_SOURCE_MODULES = [
        'data-warga',
        'data-kegiatan-warga',
        'bkl',
        'bkr',
        'paar',
    ];

    public function __construct(
        private readonly DashboardGroupCoverageRepositoryInterface $dashboardGroupCoverageRepository,
        private readonly RoleMenuVisibilityService $roleMenuVisibilityService,
        private readonly UserAreaContextService $userAreaContextService
    ) {}

    /**
     * @return array{
     *     key: string,
     *     items: list<array{
     *         slug: string,
     *         label: string,
     *         total: int,
     *         per_module: array<string, int>
     *     }>,
     *     tracked_modules: list<string>
     * }|null
     */
    public function execute(User $user, string $blockKey): ?array
    {
        $effectiveScope = $this->userAreaContextService->resolveEffectiveScope($user);
        if ($effectiveScope !== ScopeLevel::KECAMATAN->value) {
            return null;
        }

        $visibility = $this->roleMenuVisibilityService->resolveForScope($user, $effectiveScope);
        $groupModes = $visibility['groups'] ?? [];
        if (! is_array($groupModes) || $groupModes === []) {
            return null;
        }

        if ($blockKey === 'documents-pokja-i-desa-breakdown') {
            if (! array_key_exists('sekretaris-tpk', $groupModes) || ! array_key_exists('pokja-i', $groupModes)) {
                return null;
            }

            return [
                'key' => $blockKey,
                'items' => $this->normalizeBreakdownItems(
                    $this->dashboardGroupCoverageRepository->buildBreakdownByDesaForModules($user, self::SECTION4_SOURCE_MODULES)
                ),
                'tracked_modules' => self::SECTION4_SOURCE_MODULES,
            ];
        }

        if (! preg_match('/^documents\-(pokja\-i|pokja\-ii|pokja\-iii|pokja\-iv)\-kecamatan\-desa\-breakdown$/', $blockKey, $matches)) {
            return null;
        }

        $groupKey = (string) ($matches[1] ?? '');
        if ($groupKey === '' || ! in_array($groupKey, self::POKJA_GROUPS, true) || ! array_key_exists($groupKey, $groupModes)) {
            return null;
        }

        return [
            'key' => $blockKey,
            'items' => $this->normalizeBreakdownItems(
                $this->dashboardGroupCoverageRepository->buildBreakdownByDesaForGroup($user, $groupKey)
            ),
            'tracked_modules' => $this->roleMenuVisibilityService->modulesForGroup($groupKey),
        ];
    }

    /**
     * @param  list<array{
     *     desa_id: int,
     *     desa_name: string,
     *     total: int,
     *     per_module: array<string, int>
     * }>  $items
     * @return list<array{
     *     slug: string,
     *     label: string,
     *     total: int,
     *     per_module: array<string, int>
     * }>
     */
    private function normalizeBreakdownItems(array $items): array
    {
        return collect($items)
            ->map(static function (array $item): array {
                $desaId = (int) ($item['desa_id'] ?? 0);

                return [
                    'slug' => sprintf('desa-%d', $desaId),
                    'label' => (string) ($item['desa_name'] ?? '-'),
                    'total' => (int) ($item['total'] ?? 0),
                    'per_module' => collect($item['per_module'] ?? [])
                        ->mapWithKeys(static fn (mixed $value, mixed $key): array => [
                            (string) $key => (int) $value,
                        ])
                        ->all(),
                ];
            })
            ->values()
            ->all();
    }
}
