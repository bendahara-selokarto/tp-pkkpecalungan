<?php

namespace App\Policies;

use App\Support\RoleScopeMatrix;

use App\Domains\Wilayah\BukuNotulenRapat\Models\BukuNotulenRapat;
use App\Domains\Wilayah\BukuNotulenRapat\Services\BukuNotulenRapatScopeService;
use App\Models\User;

class BukuNotulenRapatPolicy
{
    use \Illuminate\Auth\Access\HandlesAuthorization;

    public function __construct(
        private readonly BukuNotulenRapatScopeService $bukuNotulenRapatScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'buku_notulen_rapat.view')
            && $this->bukuNotulenRapatScopeService->canAccessGroup($user);
    }

    public function create(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'buku_notulen_rapat.create')
            && $this->bukuNotulenRapatScopeService->canAccessGroup($user)
            && $this->bukuNotulenRapatScopeService->canEnterModule($user);
    }

    public function view(User $user, BukuNotulenRapat $bukuNotulenRapat): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'buku_notulen_rapat.view')) {
            return false;
        }

        if (! $this->bukuNotulenRapatScopeService->canAccessGroup($user)) {
            return false;
        }

        return $this->bukuNotulenRapatScopeService->canView($user, $bukuNotulenRapat);
    }

    public function update(User $user, BukuNotulenRapat $bukuNotulenRapat): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'buku_notulen_rapat.update')) {
            return false;
        }

        if (! $this->bukuNotulenRapatScopeService->canAccessGroup($user)) {
            return false;
        }

        return $this->bukuNotulenRapatScopeService->canUpdate($user, $bukuNotulenRapat);
    }

    public function delete(User $user, BukuNotulenRapat $bukuNotulenRapat): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'buku_notulen_rapat.delete')) {
            return false;
        }

        return $this->view($user, $bukuNotulenRapat);
    }
}
