<?php

namespace App\Domains\Wilayah\AccessControl\Actions;

use App\Domains\Wilayah\AccessControl\Repositories\ModuleAccessOverrideRepositoryInterface;
use App\Domains\Wilayah\Services\RoleMenuVisibilityService;
use App\Models\User;
use App\Support\RoleScopeMatrix;
use InvalidArgumentException;

class UpsertPilotCatatanKeluargaOverrideAction
{
    public function __construct(
        private readonly ModuleAccessOverrideRepositoryInterface $moduleAccessOverrideRepository,
        private readonly RoleMenuVisibilityService $roleMenuVisibilityService
    ) {
    }

    /**
     * @return array{before_mode: string, after_mode: string}
     */
    public function execute(string $scope, string $roleName, string $mode, User $actor): array
    {
        $this->ensureCompatibleScopeRole($scope, $roleName);

        $beforeMode = $this->resolveEffectiveMode($scope, $roleName);

        $override = $this->moduleAccessOverrideRepository->upsertMode(
            $scope,
            $roleName,
            RoleMenuVisibilityService::PILOT_MODULE_SLUG,
            $mode,
            (int) $actor->id
        );

        $afterMode = $this->resolveEffectiveMode($scope, $roleName);

        $this->moduleAccessOverrideRepository->storeAudit(
            (int) $override->id,
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
        ];
    }

    private function ensureCompatibleScopeRole(string $scope, string $roleName): void
    {
        if ($roleName === 'super-admin' || ! RoleScopeMatrix::isRoleCompatibleWithScope($roleName, $scope)) {
            throw new InvalidArgumentException('Kombinasi role dan scope tidak valid untuk pilot override.');
        }
    }

    private function resolveEffectiveMode(string $scope, string $roleName): string
    {
        $mode = $this->roleMenuVisibilityService
            ->resolveModuleModeForRoleScope($roleName, $scope, RoleMenuVisibilityService::PILOT_MODULE_SLUG);

        return is_string($mode) ? $mode : RoleMenuVisibilityService::MODE_HIDDEN;
    }
}

