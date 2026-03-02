<?php

namespace App\Domains\Wilayah\AccessControl\Repositories;

use App\Domains\Wilayah\AccessControl\Models\ModuleAccessOverride;
use App\Domains\Wilayah\AccessControl\Models\ModuleAccessOverrideAudit;

class ModuleAccessOverrideRepository implements ModuleAccessOverrideRepositoryInterface
{
    public function findMode(string $scope, string $roleName, string $moduleSlug): ?string
    {
        return ModuleAccessOverride::query()
            ->where('scope', $scope)
            ->where('role_name', $roleName)
            ->where('module_slug', $moduleSlug)
            ->value('mode');
    }

    public function listModesForScopeRolesAndModule(string $scope, array $roleNames, string $moduleSlug): array
    {
        if ($roleNames === []) {
            return [];
        }

        return ModuleAccessOverride::query()
            ->where('scope', $scope)
            ->where('module_slug', $moduleSlug)
            ->whereIn('role_name', $roleNames)
            ->pluck('mode', 'role_name')
            ->mapWithKeys(static fn (string $mode, string $roleName): array => [
                $roleName => $mode,
            ])
            ->all();
    }

    public function listModesForModule(string $moduleSlug): array
    {
        return ModuleAccessOverride::query()
            ->where('module_slug', $moduleSlug)
            ->get(['scope', 'role_name', 'mode'])
            ->mapWithKeys(static fn (ModuleAccessOverride $override): array => [
                sprintf('%s|%s', $override->scope, $override->role_name) => (string) $override->mode,
            ])
            ->all();
    }

    public function upsertMode(string $scope, string $roleName, string $moduleSlug, string $mode, int $changedBy): ModuleAccessOverride
    {
        return ModuleAccessOverride::query()->updateOrCreate(
            [
                'scope' => $scope,
                'role_name' => $roleName,
                'module_slug' => $moduleSlug,
            ],
            [
                'mode' => $mode,
                'changed_by' => $changedBy,
            ]
        );
    }

    public function deleteMode(string $scope, string $roleName, string $moduleSlug): bool
    {
        return ModuleAccessOverride::query()
            ->where('scope', $scope)
            ->where('role_name', $roleName)
            ->where('module_slug', $moduleSlug)
            ->delete() > 0;
    }

    public function storeAudit(
        ?int $overrideId,
        string $scope,
        string $roleName,
        string $moduleSlug,
        ?string $beforeMode,
        ?string $afterMode,
        int $changedBy
    ): void {
        ModuleAccessOverrideAudit::query()->create([
            'module_access_override_id' => $overrideId,
            'scope' => $scope,
            'role_name' => $roleName,
            'module_slug' => $moduleSlug,
            'before_mode' => $beforeMode,
            'after_mode' => $afterMode,
            'changed_by' => $changedBy,
            'changed_at' => now(),
        ]);
    }
}

