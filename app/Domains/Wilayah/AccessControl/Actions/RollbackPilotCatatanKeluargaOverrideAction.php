<?php

namespace App\Domains\Wilayah\AccessControl\Actions;

use App\Domains\Wilayah\AccessControl\Repositories\ModuleAccessOverrideRepositoryInterface;
use App\Domains\Wilayah\Services\RoleMenuVisibilityService;
use App\Models\User;
use App\Support\RoleScopeMatrix;
use InvalidArgumentException;

class RollbackPilotCatatanKeluargaOverrideAction
{
    public function __construct(
        private readonly ModuleAccessOverrideRepositoryInterface $moduleAccessOverrideRepository,
        private readonly RoleMenuVisibilityService $roleMenuVisibilityService
    ) {
    }

    /**
     * @return array{before_mode: string, after_mode: string, rolled_back: bool}
     */
    public function execute(string $scope, string $roleName, string $moduleSlug, User $actor): array
    {
        $this->ensureCompatibleScopeRole($scope, $roleName, $moduleSlug);

        $beforeMode = $this->resolveEffectiveMode($scope, $roleName, $moduleSlug);
        $deleted = $this->moduleAccessOverrideRepository->deleteMode(
            $scope,
            $roleName,
            $moduleSlug
        );

        $afterMode = $this->resolveEffectiveMode($scope, $roleName, $moduleSlug);

        $this->moduleAccessOverrideRepository->storeAudit(
            null,
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
            'rolled_back' => $deleted,
        ];
    }

    private function ensureCompatibleScopeRole(string $scope, string $roleName, string $moduleSlug): void
    {
        if ($roleName === 'super-admin' || ! RoleScopeMatrix::isRoleCompatibleWithScope($roleName, $scope)) {
            throw new InvalidArgumentException('Kombinasi role dan scope tidak valid untuk rollback pilot.');
        }

        if (! $this->roleMenuVisibilityService->isOverrideManageableModule($moduleSlug)) {
            throw new InvalidArgumentException('Modul tidak termasuk rollout override.');
        }

        if (! $this->roleMenuVisibilityService->isModuleAssignableForRoleScope($moduleSlug, $roleName, $scope)) {
            throw new InvalidArgumentException('Modul tidak kompatibel untuk role dan scope ini.');
        }
    }

    private function resolveEffectiveMode(string $scope, string $roleName, string $moduleSlug): string
    {
        $mode = $this->roleMenuVisibilityService
            ->resolveModuleModeForRoleScope($roleName, $scope, $moduleSlug);

        return is_string($mode) ? $mode : RoleMenuVisibilityService::MODE_HIDDEN;
    }
}
