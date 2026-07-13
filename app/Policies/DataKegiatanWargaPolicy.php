<?php

namespace App\Policies;

use App\Support\RoleScopeMatrix;

use App\Domains\Wilayah\DataKegiatanWarga\Models\DataKegiatanWarga;
use App\Domains\Wilayah\DataKegiatanWarga\Services\DataKegiatanWargaScopeService;
use App\Models\User;

class DataKegiatanWargaPolicy
{
    use \Illuminate\Auth\Access\HandlesAuthorization;

    public function __construct(
        private readonly DataKegiatanWargaScopeService $dataKegiatanWargaScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'data_kegiatan_warga.view');
    }

    public function create(User $user): bool
    {
        $hasPerm = RoleScopeMatrix::userHasPermission($user, 'data_kegiatan_warga.create');
        if (!$hasPerm) {
            dd('Permission missing', $user->id, $user->getRoleNames());
        }
        return $hasPerm;
    }

    public function view(User $user, DataKegiatanWarga $dataKegiatanWarga): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'data_kegiatan_warga.view')) {
            return false;
        }

        return $this->dataKegiatanWargaScopeService->canView($user, $dataKegiatanWarga);
    }

    public function update(User $user, DataKegiatanWarga $dataKegiatanWarga): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'data_kegiatan_warga.update')) {
            return false;
        }

        return $this->dataKegiatanWargaScopeService->canUpdate($user, $dataKegiatanWarga);
    }

    public function delete(User $user, DataKegiatanWarga $dataKegiatanWarga): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'data_kegiatan_warga.delete')) {
            return false;
        }

        return $this->view($user, $dataKegiatanWarga);
    }
}
