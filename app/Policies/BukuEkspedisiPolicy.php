<?php

namespace App\Policies;

use App\Support\RoleScopeMatrix;
use App\Domains\Wilayah\BukuEkspedisi\Models\BukuEkspedisi;
use App\Domains\Wilayah\BukuEkspedisi\Services\BukuEkspedisiScopeService;
use App\Models\User;

class BukuEkspedisiPolicy
{
    use \Illuminate\Auth\Access\HandlesAuthorization;

    public function __construct(
        private readonly BukuEkspedisiScopeService $bukuEkspedisiScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'buku_ekspedisi.view');
    }

    public function create(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'buku_ekspedisi.create');
    }

    public function view(User $user, BukuEkspedisi $bukuEkspedisi): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'buku_ekspedisi.view')) {
            return false;
        }

        return $this->bukuEkspedisiScopeService->canView($user, $bukuEkspedisi);
    }

    public function update(User $user, BukuEkspedisi $bukuEkspedisi): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'buku_ekspedisi.update')) {
            return false;
        }

        return $this->bukuEkspedisiScopeService->canUpdate($user, $bukuEkspedisi);
    }

    public function delete(User $user, BukuEkspedisi $bukuEkspedisi): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'buku_ekspedisi.delete')) {
            return false;
        }

        return $this->view($user, $bukuEkspedisi);
    }
}
