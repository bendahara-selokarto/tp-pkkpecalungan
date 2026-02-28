<?php

namespace App\Domains\Wilayah\AccessControl\Repositories;

use App\Domains\Wilayah\AccessControl\Models\ModuleAccessOverride;

interface ModuleAccessOverrideRepositoryInterface
{
    public function findMode(string $scope, string $roleName, string $moduleSlug): ?string;

    /**
     * @param list<string> $roleNames
     * @return array<string, string>
     */
    public function listModesForScopeRolesAndModule(string $scope, array $roleNames, string $moduleSlug): array;

    /**
     * @return array<string, string>
     */
    public function listModesForModule(string $moduleSlug): array;

    public function upsertMode(string $scope, string $roleName, string $moduleSlug, string $mode, int $changedBy): ModuleAccessOverride;

    public function deleteMode(string $scope, string $roleName, string $moduleSlug): bool;

    public function storeAudit(
        ?int $overrideId,
        string $scope,
        string $roleName,
        string $moduleSlug,
        ?string $beforeMode,
        ?string $afterMode,
        int $changedBy
    ): void;
}

