<?php

namespace App\Policies;

use App\Support\RoleScopeMatrix;

use App\Domains\Wilayah\Kliping\Models\Kliping;
use App\Domains\Wilayah\Kliping\Services\KlipingScopeService;
use App\Models\User;

class KlipingPolicy
{
    use \Illuminate\Auth\Access\HandlesAuthorization;

    public function __construct(
        private readonly KlipingScopeService $klipingScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'buku_kliping.view');
    }

    public function create(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'buku_kliping.create');
    }

    public function view(User $user, Kliping $kliping): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'buku_kliping.view')) {
            return false;
        }

        return $this->klipingScopeService->canView($user, $kliping);
    }

    public function update(User $user, Kliping $kliping): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'buku_kliping.update')) {
            return false;
        }

        return $this->klipingScopeService->canUpdate($user, $kliping);
    }

    public function delete(User $user, Kliping $kliping): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'buku_kliping.delete')) {
            return false;
        }

        return $this->view($user, $kliping);
    }
}
