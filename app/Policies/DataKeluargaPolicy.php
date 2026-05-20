<?php

namespace App\Policies;

use App\Support\RoleScopeMatrix;

use App\Domains\Wilayah\DataKeluarga\Models\DataKeluarga;
use App\Domains\Wilayah\DataKeluarga\Services\DataKeluargaScopeService;
use App\Models\User;

class DataKeluargaPolicy
{
    use \Illuminate\Auth\Access\HandlesAuthorization;

    public function __construct(
        private readonly DataKeluargaScopeService $dataKeluargaScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'data_keluarga.view');
    }

    public function create(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'data_keluarga.create');
    }

    public function view(User $user, DataKeluarga $dataKeluarga): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'data_keluarga.view')) {
            return false;
        }

        return $this->dataKeluargaScopeService->canView($user, $dataKeluarga);
    }

    public function update(User $user, DataKeluarga $dataKeluarga): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'data_keluarga.update')) {
            return false;
        }

        return $this->dataKeluargaScopeService->canUpdate($user, $dataKeluarga);
    }

    public function delete(User $user, DataKeluarga $dataKeluarga): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'data_keluarga.delete')) {
            return false;
        }

        return $this->view($user, $dataKeluarga);
    }
}

