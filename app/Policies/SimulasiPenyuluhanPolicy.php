<?php

namespace App\Policies;

use App\Support\RoleScopeMatrix;

use App\Domains\Wilayah\SimulasiPenyuluhan\Models\SimulasiPenyuluhan;
use App\Domains\Wilayah\SimulasiPenyuluhan\Services\SimulasiPenyuluhanScopeService;
use App\Models\User;

class SimulasiPenyuluhanPolicy
{
    use \Illuminate\Auth\Access\HandlesAuthorization;

    public function __construct(
        private readonly SimulasiPenyuluhanScopeService $simulasiPenyuluhanScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'simulasi_penyuluhan.view');
    }

    public function create(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'simulasi_penyuluhan.create');
    }

    public function view(User $user, SimulasiPenyuluhan $simulasiPenyuluhan): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'simulasi_penyuluhan.view')) {
            return false;
        }

        return $this->simulasiPenyuluhanScopeService->canView($user, $simulasiPenyuluhan);
    }

    public function update(User $user, SimulasiPenyuluhan $simulasiPenyuluhan): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'simulasi_penyuluhan.update')) {
            return false;
        }

        return $this->simulasiPenyuluhanScopeService->canUpdate($user, $simulasiPenyuluhan);
    }

    public function delete(User $user, SimulasiPenyuluhan $simulasiPenyuluhan): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'simulasi_penyuluhan.delete')) {
            return false;
        }

        return $this->view($user, $simulasiPenyuluhan);
    }
}
