<?php

namespace App\Policies;

use App\Support\RoleScopeMatrix;

use App\Domains\Wilayah\Koperasi\Models\Koperasi;
use App\Domains\Wilayah\Koperasi\Services\KoperasiScopeService;
use App\Models\User;

class KoperasiPolicy
{
    use \Illuminate\Auth\Access\HandlesAuthorization;

    public function __construct(
        private readonly KoperasiScopeService $koperasiScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'koperasi.view');
    }

    public function create(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'koperasi.create');
    }

    public function view(User $user, Koperasi $koperasi): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'koperasi.view')) {
            return false;
        }

        return $this->koperasiScopeService->canView($user, $koperasi);
    }

    public function update(User $user, Koperasi $koperasi): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'koperasi.update')) {
            return false;
        }

        return $this->koperasiScopeService->canUpdate($user, $koperasi);
    }

    public function delete(User $user, Koperasi $koperasi): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'koperasi.delete')) {
            return false;
        }

        return $this->view($user, $koperasi);
    }
}
