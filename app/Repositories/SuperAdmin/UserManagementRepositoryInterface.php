<?php

namespace App\Repositories\SuperAdmin;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface UserManagementRepositoryInterface
{
    public function paginateUsers(int $perPage = 10): LengthAwarePaginator;

    public function allRoleNames(): Collection;

    public function allAreas(): Collection;
}

