<?php

namespace App\UseCases\User;

use App\Repositories\SuperAdmin\UserManagementRepositoryInterface;
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
}

