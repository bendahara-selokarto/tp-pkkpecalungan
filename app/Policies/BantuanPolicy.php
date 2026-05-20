<?php

namespace App\Policies;

use App\Support\RoleScopeMatrix;

use App\Domains\Wilayah\Bantuan\Models\Bantuan;
use App\Domains\Wilayah\Bantuan\Services\BantuanScopeService;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BantuanPolicy
{
    use HandlesAuthorization;

    public function __construct(
        private readonly BantuanScopeService $bantuanScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'bantuan.view');
    }

    public function create(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'bantuan.create');
    }

    public function view(User $user, Bantuan $bantuan): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'bantuan.view')) {
            return false;
        }

        return $this->bantuanScopeService->canView($user, $bantuan);
    }

    public function update(User $user, Bantuan $bantuan): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'bantuan.update')) {
            return false;
        }

        return $this->bantuanScopeService->canUpdate($user, $bantuan);
    }

    public function delete(User $user, Bantuan $bantuan): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'bantuan.delete')) {
            return false;
        }

        return $this->view($user, $bantuan);
    }

    public function print(User $user, Bantuan $bantuan): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'bantuan.print')) {
            return false;
        }

        return $this->view($user, $bantuan);
    }
}
