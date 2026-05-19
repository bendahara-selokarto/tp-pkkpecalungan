<?php

namespace App\Policies;

use App\Domains\Wilayah\KaderKhusus\Models\KaderKhusus;
use App\Domains\Wilayah\KaderKhusus\Services\KaderKhususScopeService;
use App\Models\User;
use App\Support\RoleScopeMatrix;
use Illuminate\Auth\Access\HandlesAuthorization;

class KaderKhususPolicy
{
    use HandlesAuthorization;

    public function __construct(
        private readonly KaderKhususScopeService $kaderKhususScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'kader_khusus.view');
    }

    public function create(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'kader_khusus.create');
    }

    public function view(User $user, KaderKhusus $kaderKhusus): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'kader_khusus.view')) {
            return false;
        }

        return $this->kaderKhususScopeService->canView($user, $kaderKhusus);
    }

    public function update(User $user, KaderKhusus $kaderKhusus): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'kader_khusus.update')) {
            return false;
        }

        return $this->kaderKhususScopeService->canUpdate($user, $kaderKhusus);
    }

    public function delete(User $user, KaderKhusus $kaderKhusus): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'kader_khusus.delete')) {
            return false;
        }

        return $this->view($user, $kaderKhusus);
    }

    public function print(User $user, KaderKhusus $kaderKhusus): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'kader_khusus.print')) {
            return false;
        }

        return $this->view($user, $kaderKhusus);
    }
}
