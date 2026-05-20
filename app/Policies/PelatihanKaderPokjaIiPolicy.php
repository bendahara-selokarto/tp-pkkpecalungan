<?php

namespace App\Policies;

use App\Support\RoleScopeMatrix;

use App\Domains\Wilayah\PelatihanKaderPokjaIi\Models\PelatihanKaderPokjaIi;
use App\Domains\Wilayah\PelatihanKaderPokjaIi\Services\PelatihanKaderPokjaIiScopeService;
use App\Models\User;

class PelatihanKaderPokjaIiPolicy
{
    use \Illuminate\Auth\Access\HandlesAuthorization;

    public function __construct(
        private readonly PelatihanKaderPokjaIiScopeService $pelatihanKaderPokjaIiScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'pelatihan_kader_pokja_ii.view');
    }

    public function create(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'pelatihan_kader_pokja_ii.create');
    }

    public function view(User $user, PelatihanKaderPokjaIi $pelatihanKaderPokjaIi): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'pelatihan_kader_pokja_ii.view')) {
            return false;
        }

        return $this->pelatihanKaderPokjaIiScopeService->canView($user, $pelatihanKaderPokjaIi);
    }

    public function update(User $user, PelatihanKaderPokjaIi $pelatihanKaderPokjaIi): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'pelatihan_kader_pokja_ii.update')) {
            return false;
        }

        return $this->pelatihanKaderPokjaIiScopeService->canUpdate($user, $pelatihanKaderPokjaIi);
    }

    public function delete(User $user, PelatihanKaderPokjaIi $pelatihanKaderPokjaIi): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'pelatihan_kader_pokja_ii.delete')) {
            return false;
        }

        return $this->view($user, $pelatihanKaderPokjaIi);
    }
}
