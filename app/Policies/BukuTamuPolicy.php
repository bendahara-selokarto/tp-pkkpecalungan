<?php

namespace App\Policies;

use App\Support\RoleScopeMatrix;

use App\Domains\Wilayah\BukuTamu\Models\BukuTamu;
use App\Domains\Wilayah\BukuTamu\Services\BukuTamuScopeService;
use App\Models\User;

class BukuTamuPolicy
{
    use \Illuminate\Auth\Access\HandlesAuthorization;

    public function __construct(
        private readonly BukuTamuScopeService $bukuTamuScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'buku_tamu.view');
    }

    public function create(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'buku_tamu.create');
    }

    public function view(User $user, BukuTamu $bukuTamu): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'buku_tamu.view')) {
            return false;
        }

        return $this->bukuTamuScopeService->canView($user, $bukuTamu);
    }

    public function update(User $user, BukuTamu $bukuTamu): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'buku_tamu.update')) {
            return false;
        }

        return $this->bukuTamuScopeService->canUpdate($user, $bukuTamu);
    }

    public function delete(User $user, BukuTamu $bukuTamu): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'buku_tamu.delete')) {
            return false;
        }

        return $this->view($user, $bukuTamu);
    }
}
