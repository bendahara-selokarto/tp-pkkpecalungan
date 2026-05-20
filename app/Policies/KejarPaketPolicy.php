<?php

namespace App\Policies;

use App\Support\RoleScopeMatrix;

use App\Domains\Wilayah\KejarPaket\Models\KejarPaket;
use App\Domains\Wilayah\KejarPaket\Services\KejarPaketScopeService;
use App\Models\User;

class KejarPaketPolicy
{
    use \Illuminate\Auth\Access\HandlesAuthorization;

    public function __construct(
        private readonly KejarPaketScopeService $kejarPaketScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'kejar_paket.view');
    }

    public function create(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'kejar_paket.create');
    }

    public function view(User $user, KejarPaket $kejarPaket): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'kejar_paket.view')) {
            return false;
        }

        return $this->kejarPaketScopeService->canView($user, $kejarPaket);
    }

    public function update(User $user, KejarPaket $kejarPaket): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'kejar_paket.update')) {
            return false;
        }

        return $this->kejarPaketScopeService->canUpdate($user, $kejarPaket);
    }

    public function delete(User $user, KejarPaket $kejarPaket): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'kejar_paket.delete')) {
            return false;
        }

        return $this->view($user, $kejarPaket);
    }
}


