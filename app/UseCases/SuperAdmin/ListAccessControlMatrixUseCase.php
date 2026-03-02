<?php

namespace App\UseCases\SuperAdmin;

use App\Domains\Wilayah\AccessControl\Repositories\ModuleAccessOverrideRepositoryInterface;
use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Domains\Wilayah\Services\RoleMenuVisibilityService;
use App\Support\RoleLabelFormatter;
use App\Support\RoleScopeMatrix;
use Illuminate\Support\Collection;

class ListAccessControlMatrixUseCase
{
    /**
     * @var array<string, string>
     */
    private const MODE_LABELS = [
        RoleMenuVisibilityService::MODE_READ_WRITE => 'Baca dan Tulis',
        RoleMenuVisibilityService::MODE_READ_ONLY => 'Baca Saja',
        RoleMenuVisibilityService::MODE_HIDDEN => 'Tidak Tampil',
    ];

    public function __construct(
        private readonly RoleMenuVisibilityService $roleMenuVisibilityService,
        private readonly ModuleAccessOverrideRepositoryInterface $moduleAccessOverrideRepository
    ) {
    }

    /**
     * @param array{scope?: string|null, role?: string|null, mode?: string|null} $filters
     * @return array{
     *     filters: array{scope: string|null, role: string|null, mode: string|null},
     *     scopeOptions: list<array{value: string, label: string}>,
     *     roleOptions: list<array{value: string, label: string, scopes: list<string>}>,
     *     modeOptions: list<array{value: string, label: string}>,
     *     rows: list<array{
     *         id: string,
     *         scope: string,
     *         scope_label: string,
     *         role: string,
     *         role_label: string,
     *         group: string,
     *         group_label: string,
     *         module: string,
     *         module_label: string,
     *         mode: string,
     *         mode_label: string,
     *         override_manageable: bool,
     *         override_active: bool,
     *         override_mode: string|null,
     *         override_baseline_mode: string,
     *         override_baseline_mode_label: string|null
     *     }>,
     *     summary: array{
     *         total_rows: int,
     *         filtered_rows: int,
     *         read_write: int,
     *         read_only: int,
     *         hidden: int
     *     }
     * }
     */
    public function execute(array $filters = []): array
    {
        $scopeFilter = $this->normalizeFilter($filters['scope'] ?? null);
        $roleFilter = $this->normalizeFilter($filters['role'] ?? null);
        $modeFilter = $this->normalizeFilter($filters['mode'] ?? null);

        $roleScopePairs = $this->roleScopePairs();
        $overrideModules = $this->roleMenuVisibilityService->overrideManageableModules();
        $overrideModesByModule = [];
        foreach ($overrideModules as $moduleSlug) {
            $overrideModesByModule[$moduleSlug] = $this->moduleAccessOverrideRepository->listModesForModule($moduleSlug);
        }

        $rows = $this->buildRows($roleScopePairs, $overrideModules, $overrideModesByModule);
        $filteredRows = $this->filterRows($rows, $scopeFilter, $roleFilter, $modeFilter);

        return [
            'filters' => [
                'scope' => $scopeFilter,
                'role' => $roleFilter,
                'mode' => $modeFilter,
            ],
            'scopeOptions' => $this->scopeOptions(),
            'roleOptions' => $this->roleOptions($roleScopePairs),
            'modeOptions' => $this->modeOptions(),
            'rows' => $filteredRows->values()->all(),
            'summary' => [
                'total_rows' => $rows->count(),
                'filtered_rows' => $filteredRows->count(),
                'read_write' => $filteredRows->where('mode', RoleMenuVisibilityService::MODE_READ_WRITE)->count(),
                'read_only' => $filteredRows->where('mode', RoleMenuVisibilityService::MODE_READ_ONLY)->count(),
                'hidden' => $filteredRows->where('mode', RoleMenuVisibilityService::MODE_HIDDEN)->count(),
            ],
        ];
    }

    /**
     * @param Collection<int, array<string, mixed>> $rows
     * @return Collection<int, array<string, mixed>>
     */
    private function filterRows(Collection $rows, ?string $scopeFilter, ?string $roleFilter, ?string $modeFilter): Collection
    {
        return $rows->filter(function (array $row) use ($scopeFilter, $roleFilter, $modeFilter): bool {
            if (is_string($scopeFilter) && $row['scope'] !== $scopeFilter) {
                return false;
            }

            if (is_string($roleFilter) && $row['role'] !== $roleFilter) {
                return false;
            }

            if (is_string($modeFilter) && $row['mode'] !== $modeFilter) {
                return false;
            }

            return true;
        });
    }

    /**
     * @param list<array{scope: string, role: string}> $roleScopePairs
     * @param list<string> $overrideModules
     * @param array<string, array<string, string>> $overrideModesByModule
     * @return Collection<int, array<string, mixed>>
     */
    private function buildRows(array $roleScopePairs, array $overrideModules, array $overrideModesByModule): Collection
    {
        $rows = collect();

        foreach ($roleScopePairs as $pair) {
            $scope = $pair['scope'];
            $role = $pair['role'];
            $groups = $this->roleMenuVisibilityService->groupsForScope($scope);
            $groupModes = $this->roleMenuVisibilityService->resolveForRoleScope($role, $scope)['groups'];
            $overrides = $this->roleMenuVisibilityService->roleModuleModeOverrides($role, $scope);

            foreach ($groups as $group) {
                $groupMode = $groupModes[$group] ?? null;

                foreach ($this->roleMenuVisibilityService->modulesForGroup($group) as $module) {
                    $mode = $this->resolveEffectiveMode($groupMode, $overrides, $module);
                    $isOverrideManageable = in_array($module, $overrideModules, true)
                        && $role !== 'super-admin'
                        && $this->roleMenuVisibilityService->isModuleAssignableForRoleScope($module, $role, $scope);
                    $overrideMode = $overrideModesByModule[$module][$this->overrideModeKey($scope, $role)] ?? null;
                    $baselineMode = $isOverrideManageable
                        ? $this->roleMenuVisibilityService->resolveBaselineModuleModeForRoleScope($role, $scope, $module)
                        : null;

                    $rows->push([
                        'id' => sprintf('%s|%s|%s|%s', $scope, $role, $group, $module),
                        'scope' => $scope,
                        'scope_label' => ucfirst($scope),
                        'role' => $role,
                        'role_label' => RoleLabelFormatter::label($role),
                        'group' => $group,
                        'group_label' => $this->groupLabel($group),
                        'module' => $module,
                        'module_label' => $this->moduleLabel($module),
                        'mode' => $mode,
                        'mode_label' => self::MODE_LABELS[$mode] ?? ucfirst(str_replace('-', ' ', $mode)),
                        'override_manageable' => $isOverrideManageable,
                        'override_active' => $isOverrideManageable && is_string($overrideMode),
                        'override_mode' => $isOverrideManageable ? $overrideMode : null,
                        'override_baseline_mode' => $isOverrideManageable && is_string($baselineMode)
                            ? $baselineMode
                            : RoleMenuVisibilityService::MODE_HIDDEN,
                        'override_baseline_mode_label' => $isOverrideManageable
                            ? (self::MODE_LABELS[$baselineMode ?? RoleMenuVisibilityService::MODE_HIDDEN] ?? '-')
                            : null,
                    ]);
                }
            }
        }

        return $rows;
    }

    private function resolveEffectiveMode(?string $groupMode, array $overrides, string $module): string
    {
        if (! is_string($groupMode)) {
            return RoleMenuVisibilityService::MODE_HIDDEN;
        }

        if (! array_key_exists($module, $overrides)) {
            return $groupMode;
        }

        $override = $overrides[$module];

        return is_string($override) ? $override : RoleMenuVisibilityService::MODE_HIDDEN;
    }

    /**
     * @return list<array{scope: string, role: string}>
     */
    private function roleScopePairs(): array
    {
        $scopedRoles = RoleScopeMatrix::scopedRoles();
        $pairs = [];

        foreach (ScopeLevel::values() as $scope) {
            $roles = array_values(array_unique([
                ...($scopedRoles[$scope] ?? []),
                'super-admin',
            ]));

            foreach ($roles as $role) {
                $pairs[] = [
                    'scope' => $scope,
                    'role' => $role,
                ];
            }
        }

        return $pairs;
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    private function scopeOptions(): array
    {
        return array_map(
            static fn (string $scope): array => [
                'value' => $scope,
                'label' => ucfirst($scope),
            ],
            ScopeLevel::values(),
        );
    }

    /**
     * @param list<array{scope: string, role: string}> $roleScopePairs
     * @return list<array{value: string, label: string, scopes: list<string>}>
     */
    private function roleOptions(array $roleScopePairs): array
    {
        return collect($roleScopePairs)
            ->groupBy('role')
            ->map(function (Collection $items, string $role): array {
                return [
                    'value' => $role,
                    'label' => RoleLabelFormatter::label($role),
                    'scopes' => $items
                        ->pluck('scope')
                        ->unique()
                        ->values()
                        ->all(),
                ];
            })
            ->sortBy('label')
            ->values()
            ->all();
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    private function modeOptions(): array
    {
        return collect(self::MODE_LABELS)
            ->map(fn (string $label, string $value): array => [
                'value' => $value,
                'label' => $label,
            ])
            ->values()
            ->all();
    }

    private function groupLabel(string $group): string
    {
        return match ($group) {
            'sekretaris-tpk' => 'Sekretaris TPK',
            'pokja-i' => 'Pokja I',
            'pokja-ii' => 'Pokja II',
            'pokja-iii' => 'Pokja III',
            'pokja-iv' => 'Pokja IV',
            'monitoring' => 'Monitoring',
            default => ucfirst(str_replace('-', ' ', $group)),
        };
    }

    private function moduleLabel(string $module): string
    {
        return ucfirst(str_replace('-', ' ', $module));
    }

    private function overrideModeKey(string $scope, string $role): string
    {
        return sprintf('%s|%s', $scope, $role);
    }

    private function normalizeFilter(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $trimmed = trim($value);

        return $trimmed !== '' ? $trimmed : null;
    }
}
