<?php

namespace App\Policies;

use App\Support\RoleScopeMatrix;

use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Models\DataPemanfaatanTanahPekaranganHatinyaPkk;
use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Services\DataPemanfaatanTanahPekaranganHatinyaPkkScopeService;
use App\Models\User;

class DataPemanfaatanTanahPekaranganHatinyaPkkPolicy
{
    use \Illuminate\Auth\Access\HandlesAuthorization;

    public function __construct(
        private readonly DataPemanfaatanTanahPekaranganHatinyaPkkScopeService $dataPemanfaatanTanahPekaranganHatinyaPkkScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'data_pemanfaatan_tanah_pekarangan_hatinya_pkk.view');
    }

    public function create(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'data_pemanfaatan_tanah_pekarangan_hatinya_pkk.create');
    }

    public function view(User $user, DataPemanfaatanTanahPekaranganHatinyaPkk $dataPemanfaatanTanahPekaranganHatinyaPkk): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'data_pemanfaatan_tanah_pekarangan_hatinya_pkk.view')) {
            return false;
        }

        return $this->dataPemanfaatanTanahPekaranganHatinyaPkkScopeService->canView($user, $dataPemanfaatanTanahPekaranganHatinyaPkk);
    }

    public function update(User $user, DataPemanfaatanTanahPekaranganHatinyaPkk $dataPemanfaatanTanahPekaranganHatinyaPkk): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'data_pemanfaatan_tanah_pekarangan_hatinya_pkk.update')) {
            return false;
        }

        return $this->dataPemanfaatanTanahPekaranganHatinyaPkkScopeService->canUpdate($user, $dataPemanfaatanTanahPekaranganHatinyaPkk);
    }

    public function delete(User $user, DataPemanfaatanTanahPekaranganHatinyaPkk $dataPemanfaatanTanahPekaranganHatinyaPkk): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'data_pemanfaatan_tanah_pekarangan_hatinya_pkk.delete')) {
            return false;
        }

        return $this->view($user, $dataPemanfaatanTanahPekaranganHatinyaPkk);
    }
}


