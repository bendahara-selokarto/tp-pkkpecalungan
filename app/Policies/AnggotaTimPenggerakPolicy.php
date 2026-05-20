<?php

namespace App\Policies;

use App\Support\RoleScopeMatrix;

use App\Domains\Wilayah\AnggotaTimPenggerak\Models\AnggotaTimPenggerak;
use App\Domains\Wilayah\AnggotaTimPenggerak\Services\AnggotaTimPenggerakScopeService;
use App\Models\User;

class AnggotaTimPenggerakPolicy
{
    use \Illuminate\Auth\Access\HandlesAuthorization;

    public function __construct(
        private readonly AnggotaTimPenggerakScopeService $anggotaTimPenggerakScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'anggota_tim_penggerak.view');
    }

    public function create(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'anggota_tim_penggerak.create');
    }

    public function view(User $user, AnggotaTimPenggerak $anggotaTimPenggerak): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'anggota_tim_penggerak.view')) {
            return false;
        }

        return $this->anggotaTimPenggerakScopeService->canView($user, $anggotaTimPenggerak);
    }

    public function update(User $user, AnggotaTimPenggerak $anggotaTimPenggerak): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'anggota_tim_penggerak.update')) {
            return false;
        }

        return $this->anggotaTimPenggerakScopeService->canUpdate($user, $anggotaTimPenggerak);
    }

    public function delete(User $user, AnggotaTimPenggerak $anggotaTimPenggerak): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'anggota_tim_penggerak.delete')) {
            return false;
        }

        return $this->view($user, $anggotaTimPenggerak);
    }
}
