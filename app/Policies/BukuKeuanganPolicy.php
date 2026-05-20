<?php

namespace App\Policies;

use App\Support\RoleScopeMatrix;

use App\Domains\Wilayah\BukuKeuangan\Models\BukuKeuangan;
use App\Domains\Wilayah\BukuKeuangan\Services\BukuKeuanganScopeService;
use App\Models\User;

class BukuKeuanganPolicy
{
    use \Illuminate\Auth\Access\HandlesAuthorization;

    public function __construct(
        private readonly BukuKeuanganScopeService $bukuKeuanganScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'buku_keuangan.view');
    }

    public function create(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'buku_keuangan.create');
    }

    public function view(User $user, BukuKeuangan $bukuKeuangan): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'buku_keuangan.view')) {
            return false;
        }

        return $this->bukuKeuanganScopeService->canView($user, $bukuKeuangan);
    }

    public function update(User $user, BukuKeuangan $bukuKeuangan): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'buku_keuangan.update')) {
            return false;
        }

        return $this->bukuKeuanganScopeService->canUpdate($user, $bukuKeuangan);
    }

    public function delete(User $user, BukuKeuangan $bukuKeuangan): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'buku_keuangan.delete')) {
            return false;
        }

        return $this->view($user, $bukuKeuangan);
    }
}
