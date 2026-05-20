<?php

namespace App\Policies;

use App\Support\RoleScopeMatrix;

use App\Domains\Wilayah\PraKoperasiUp2k\Models\PraKoperasiUp2k;
use App\Domains\Wilayah\PraKoperasiUp2k\Services\PraKoperasiUp2kScopeService;
use App\Models\User;

class PraKoperasiUp2kPolicy
{
    use \Illuminate\Auth\Access\HandlesAuthorization;

    public function __construct(
        private readonly PraKoperasiUp2kScopeService $praKoperasiUp2kScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'pra_koperasi_up2k.view');
    }

    public function create(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'pra_koperasi_up2k.create');
    }

    public function view(User $user, PraKoperasiUp2k $praKoperasiUp2k): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'pra_koperasi_up2k.view')) {
            return false;
        }

        return $this->praKoperasiUp2kScopeService->canView($user, $praKoperasiUp2k);
    }

    public function update(User $user, PraKoperasiUp2k $praKoperasiUp2k): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'pra_koperasi_up2k.update')) {
            return false;
        }

        return $this->praKoperasiUp2kScopeService->canUpdate($user, $praKoperasiUp2k);
    }

    public function delete(User $user, PraKoperasiUp2k $praKoperasiUp2k): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'pra_koperasi_up2k.delete')) {
            return false;
        }

        return $this->view($user, $praKoperasiUp2k);
    }
}
