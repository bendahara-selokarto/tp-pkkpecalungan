<?php

namespace App\Domains\Wilayah\AccessControl\Actions;

use App\Domains\Wilayah\AccessControl\Repositories\ModuleAccessOverrideRepositoryInterface;
use App\Domains\Wilayah\Services\RoleMenuVisibilityService;
use App\Models\User;
use App\Support\RoleScopeMatrix;
use InvalidArgumentException;

class UpsertPilotModuleOverrideAction
{
    public function __construct(
        private readonly ModuleAccessOverrideRepositoryInterface $moduleAccessOverrideRepository,
        private readonly RoleMenuVisibilityService $roleMenuVisibilityService
    ) {
    }

    /**
     * @return array{before_mode: string, after_mode: string}
     */
    public function execute(string $scope, string $roleName, string $moduleSlug, string $mode, User $actor): array
    {
        $this->ensureCompatibleScopeRole($scope, $roleName);
        $this->ensurePilotModuleManaged($moduleSlug);

        $beforeMode = $this->resolveEffectiveMode($scope, $roleName, $moduleSlug);

        $override = $this->moduleAccessOverrideRepository->upsertMode(
            $scope,
            $roleName,
            $moduleSlug,
            $mode,
            (int) $actor->id
        );

        $afterMode = $this->resolveEffectiveMode($scope, $roleName, $moduleSlug);

        $this->moduleAccessOverrideRepository->storeAudit(
            (int) $override->id,
            $scope,
            $roleName,
            $moduleSlug,
            $beforeMode,
            $afterMode,
            (int) $actor->id
        );

        return [
            'before_mode' => $beforeMode,
            'after_mode' => $afterMode,
        ];
    }

    private function ensureCompatibleScopeRole(string $scope, string $roleName): void
    {
        if ($roleName === 'super-admin' || ! RoleScopeMatrix::isRoleCompatibleWithScope($roleName, $scope)) {
            throw new InvalidArgumentException('Kombinasi role dan scope tidak valid untuk pilot override.');
        }
    }

    private function ensurePilotModuleManaged(string $moduleSlug): void
    {
        if (! $this->roleMenuVisibilityService->isPilotModuleManaged($moduleSlug)) {
            throw new InvalidArgumentException('Modul pilot tidak dikelola pada konfigurasi akses kontrol.');
        }
    }

    private function resolveEffectiveMode(string $scope, string $roleName, string $moduleSlug): string
    {
        $mode = $this->roleMenuVisibilityService
            ->resolveModuleModeForRoleScope($roleName, $scope, $moduleSlug);

        return is_string($mode) ? $mode : RoleMenuVisibilityService::MODE_HIDDEN;
    }
}
