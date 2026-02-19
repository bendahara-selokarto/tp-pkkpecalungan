<?php

namespace App\UseCases\User;

use App\Repositories\SuperAdmin\UserManagementRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListUsersForManagementUseCase
{
    public function __construct(
        private readonly UserManagementRepositoryInterface $userManagementRepository
    ) {
    }

    public function execute(int $perPage = 10): LengthAwarePaginator
    {
        return $this->userManagementRepository->paginateUsers($perPage);
    }
}

