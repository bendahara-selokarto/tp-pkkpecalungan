<?php

namespace App\Policies;

use App\Support\RoleScopeMatrix;

use App\Domains\Wilayah\DataPelatihanKader\Models\DataPelatihanKader;
use App\Domains\Wilayah\DataPelatihanKader\Services\DataPelatihanKaderScopeService;
use App\Models\User;

class DataPelatihanKaderPolicy
{
    use \Illuminate\Auth\Access\HandlesAuthorization;

    public function __construct(
        private readonly DataPelatihanKaderScopeService $dataPelatihanKaderScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'data_pelatihan_kader.view');
    }

    public function create(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'data_pelatihan_kader.create');
    }

    public function view(User $user, DataPelatihanKader $dataPelatihanKader): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'data_pelatihan_kader.view')) {
            return false;
        }

        return $this->dataPelatihanKaderScopeService->canView($user, $dataPelatihanKader);
    }

    public function update(User $user, DataPelatihanKader $dataPelatihanKader): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'data_pelatihan_kader.update')) {
            return false;
        }

        return $this->dataPelatihanKaderScopeService->canUpdate($user, $dataPelatihanKader);
    }

    public function delete(User $user, DataPelatihanKader $dataPelatihanKader): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'data_pelatihan_kader.delete')) {
            return false;
        }

        return $this->view($user, $dataPelatihanKader);
    }
}
