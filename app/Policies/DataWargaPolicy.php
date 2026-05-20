<?php

namespace App\Policies;

use App\Support\RoleScopeMatrix;

use App\Domains\Wilayah\DataWarga\Models\DataWarga;
use App\Domains\Wilayah\DataWarga\Services\DataWargaScopeService;
use App\Models\User;

class DataWargaPolicy
{
    use \Illuminate\Auth\Access\HandlesAuthorization;

    public function __construct(
        private readonly DataWargaScopeService $dataWargaScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'data_warga.view');
    }

    public function create(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'data_warga.create');
    }

    public function view(User $user, DataWarga $dataWarga): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'data_warga.view')) {
            return false;
        }

        return $this->dataWargaScopeService->canView($user, $dataWarga);
    }

    public function update(User $user, DataWarga $dataWarga): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'data_warga.update')) {
            return false;
        }

        return $this->dataWargaScopeService->canUpdate($user, $dataWarga);
    }

    public function delete(User $user, DataWarga $dataWarga): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'data_warga.delete')) {
            return false;
        }

        return $this->view($user, $dataWarga);
    }
}
