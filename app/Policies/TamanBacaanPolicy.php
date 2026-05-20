<?php

namespace App\Policies;

use App\Support\RoleScopeMatrix;

use App\Domains\Wilayah\TamanBacaan\Models\TamanBacaan;
use App\Domains\Wilayah\TamanBacaan\Services\TamanBacaanScopeService;
use App\Models\User;

class TamanBacaanPolicy
{
    use \Illuminate\Auth\Access\HandlesAuthorization;

    public function __construct(
        private readonly TamanBacaanScopeService $tamanBacaanScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'taman_bacaan.view');
    }

    public function create(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'taman_bacaan.create');
    }

    public function view(User $user, TamanBacaan $tamanBacaan): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'taman_bacaan.view')) {
            return false;
        }

        return $this->tamanBacaanScopeService->canView($user, $tamanBacaan);
    }

    public function update(User $user, TamanBacaan $tamanBacaan): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'taman_bacaan.update')) {
            return false;
        }

        return $this->tamanBacaanScopeService->canUpdate($user, $tamanBacaan);
    }

    public function delete(User $user, TamanBacaan $tamanBacaan): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'taman_bacaan.delete')) {
            return false;
        }

        return $this->view($user, $tamanBacaan);
    }
}

