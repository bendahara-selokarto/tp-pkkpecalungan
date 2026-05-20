<?php

namespace App\Policies;

use App\Support\RoleScopeMatrix;

use App\Domains\Wilayah\DataIndustriRumahTangga\Models\DataIndustriRumahTangga;
use App\Domains\Wilayah\DataIndustriRumahTangga\Services\DataIndustriRumahTanggaScopeService;
use App\Models\User;

class DataIndustriRumahTanggaPolicy
{
    use \Illuminate\Auth\Access\HandlesAuthorization;

    public function __construct(
        private readonly DataIndustriRumahTanggaScopeService $dataIndustriRumahTanggaScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'data_industri_rumah_tangga.view');
    }

    public function create(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'data_industri_rumah_tangga.create');
    }

    public function view(User $user, DataIndustriRumahTangga $dataIndustriRumahTangga): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'data_industri_rumah_tangga.view')) {
            return false;
        }

        return $this->dataIndustriRumahTanggaScopeService->canView($user, $dataIndustriRumahTangga);
    }

    public function update(User $user, DataIndustriRumahTangga $dataIndustriRumahTangga): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'data_industri_rumah_tangga.update')) {
            return false;
        }

        return $this->dataIndustriRumahTanggaScopeService->canUpdate($user, $dataIndustriRumahTangga);
    }

    public function delete(User $user, DataIndustriRumahTangga $dataIndustriRumahTangga): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'data_industri_rumah_tangga.delete')) {
            return false;
        }

        return $this->view($user, $dataIndustriRumahTangga);
    }
}



