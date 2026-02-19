<?php

namespace App\Repositories\SuperAdmin;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role;

class UserManagementRepository implements UserManagementRepositoryInterface
{
    public function paginateUsers(int $perPage = 10): LengthAwarePaginator
    {
        return User::query()
            ->with(['roles:id,name', 'area:id,name,level'])
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function allRoleNames(): Collection
    {
        return Role::query()->pluck('name')->values();
    }

    public function allAreas(): Collection
    {
        return Area::query()
            ->orderBy('level')
            ->orderBy('name')
            ->get(['id', 'name', 'level']);
    }
}

