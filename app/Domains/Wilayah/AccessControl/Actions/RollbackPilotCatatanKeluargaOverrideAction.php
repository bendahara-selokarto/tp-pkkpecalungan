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
    public function execute(string $scope, string $roleName, User $actor): array
    {
        $this->ensureCompatibleScopeRole($scope, $roleName);

        $beforeMode = $this->resolveEffectiveMode($scope, $roleName);
        $deleted = $this->moduleAccessOverrideRepository->deleteMode(
            $scope,
            $roleName,
            RoleMenuVisibilityService::PILOT_MODULE_SLUG
        );

        $afterMode = $this->resolveEffectiveMode($scope, $roleName);

        $this->moduleAccessOverrideRepository->storeAudit(
            null,
            $scope,
            $roleName,
            RoleMenuVisibilityService::PILOT_MODULE_SLUG,
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

    private function ensureCompatibleScopeRole(string $scope, string $roleName): void
    {
        if ($roleName === 'super-admin' || ! RoleScopeMatrix::isRoleCompatibleWithScope($roleName, $scope)) {
            throw new InvalidArgumentException('Kombinasi role dan scope tidak valid untuk rollback pilot.');
        }
    }

    private function resolveEffectiveMode(string $scope, string $roleName): string
    {
        $mode = $this->roleMenuVisibilityService
            ->resolveModuleModeForRoleScope($roleName, $scope, RoleMenuVisibilityService::PILOT_MODULE_SLUG);

        return is_string($mode) ? $mode : RoleMenuVisibilityService::MODE_HIDDEN;
    }
}

