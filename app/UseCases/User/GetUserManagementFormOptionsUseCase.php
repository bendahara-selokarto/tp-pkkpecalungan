<?php

namespace App\UseCases\User;

use App\Repositories\SuperAdmin\UserManagementRepositoryInterface;
use App\Support\RoleLabelFormatter;
use App\Support\RoleScopeMatrix;
use Illuminate\Support\Collection;

class GetUserManagementFormOptionsUseCase
{
    public function __construct(
        private readonly UserManagementRepositoryInterface $userManagementRepository
    ) {
    }

    public function roles(): Collection
    {
        return $this->userManagementRepository->allRoleNames();
    }

    public function areas(): Collection
    {
        return $this->userManagementRepository->allAreas();
    }

    /**
     * @return array<string, list<string>>
     */
    public function roleOptionsByScope(): array
    {
        $existingRoles = $this->roles()->all();
        $options = [];

        foreach (['desa', 'kecamatan'] as $scope) {
            $options[$scope] = array_values(array_filter(
                RoleScopeMatrix::assignableRolesForScope($scope),
                static fn (string $role): bool => in_array($role, $existingRoles, true)
            ));
        }

        return $options;
    }

    /**
     * @return array<string, string>
     */
    public function roleLabels(array $roleOptionsByScope): array
    {
        $labels = [];
        $allRoles = array_unique(array_merge(
            $roleOptionsByScope['desa'] ?? [],
            $roleOptionsByScope['kecamatan'] ?? []
        ));

        foreach ($allRoles as $role) {
            $labels[$role] = RoleLabelFormatter::label($role);
        }

        return $labels;
    }
}
