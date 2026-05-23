<?php

namespace App\Policies;

use App\Domains\Wilayah\FotoKegiatan\Models\FotoKegiatan;
use App\Domains\Wilayah\FotoKegiatan\Services\FotoKegiatanScopeService;
use App\Models\User;
use App\Support\RoleScopeMatrix;
use Illuminate\Auth\Access\HandlesAuthorization;

class FotoKegiatanPolicy
{
    use HandlesAuthorization;

    public function __construct(
        private readonly FotoKegiatanScopeService $fotoKegiatanScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'foto_kegiatan.view');
    }

    public function view(User $user, FotoKegiatan $fotoKegiatan): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'foto_kegiatan.view')) {
            return false;
        }

        return $this->fotoKegiatanScopeService->canView($user, $fotoKegiatan);
    }

    public function create(User $user): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'foto_kegiatan.create')) {
            return false;
        }

        return $this->fotoKegiatanScopeService->canEnterModule($user);
    }

    public function update(User $user, FotoKegiatan $fotoKegiatan): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'foto_kegiatan.update')) {
            return false;
        }

        return $this->fotoKegiatanScopeService->canUpdate($user, $fotoKegiatan);
    }

    public function delete(User $user, FotoKegiatan $fotoKegiatan): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'foto_kegiatan.delete')) {
            return false;
        }

        return $this->fotoKegiatanScopeService->canUpdate($user, $fotoKegiatan);
    }
}
