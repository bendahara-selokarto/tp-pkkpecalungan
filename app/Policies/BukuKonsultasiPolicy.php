<?php

namespace App\Policies;

use App\Domains\Wilayah\BukuKonsultasi\Models\BukuKonsultasi;
use App\Domains\Wilayah\BukuKonsultasi\Services\BukuKonsultasiScopeService;
use App\Models\User;
use App\Support\RoleScopeMatrix;
use Illuminate\Auth\Access\HandlesAuthorization;

class BukuKonsultasiPolicy
{
    use HandlesAuthorization;

    public function __construct(
        private readonly BukuKonsultasiScopeService $bukuKonsultasiScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'buku_konsultasi.view');
    }

    public function view(User $user, BukuKonsultasi $bukuKonsultasi): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'buku_konsultasi.view')) {
            return false;
        }

        return $this->bukuKonsultasiScopeService->canView($user, $bukuKonsultasi);
    }

    public function create(User $user): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'buku_konsultasi.create')) {
            return false;
        }

        return $this->bukuKonsultasiScopeService->canEnterModule($user);
    }

    public function update(User $user, BukuKonsultasi $bukuKonsultasi): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'buku_konsultasi.update')) {
            return false;
        }

        return $this->bukuKonsultasiScopeService->canUpdate($user, $bukuKonsultasi);
    }

    public function delete(User $user, BukuKonsultasi $bukuKonsultasi): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'buku_konsultasi.delete')) {
            return false;
        }

        return $this->bukuKonsultasiScopeService->canUpdate($user, $bukuKonsultasi);
    }
}
