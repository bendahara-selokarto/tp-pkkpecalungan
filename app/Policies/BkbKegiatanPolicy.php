<?php

namespace App\Policies;

use App\Support\RoleScopeMatrix;

use App\Domains\Wilayah\BkbKegiatan\Models\BkbKegiatan;
use App\Domains\Wilayah\BkbKegiatan\Services\BkbKegiatanScopeService;
use App\Models\User;

class BkbKegiatanPolicy
{
    use \Illuminate\Auth\Access\HandlesAuthorization;

    public function __construct(
        private readonly BkbKegiatanScopeService $bkbKegiatanScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'bkb_kegiatan.view');
    }

    public function create(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'bkb_kegiatan.create');
    }

    public function view(User $user, BkbKegiatan $bkbKegiatan): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'bkb_kegiatan.view')) {
            return false;
        }

        return $this->bkbKegiatanScopeService->canView($user, $bkbKegiatan);
    }

    public function update(User $user, BkbKegiatan $bkbKegiatan): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'bkb_kegiatan.update')) {
            return false;
        }

        return $this->bkbKegiatanScopeService->canUpdate($user, $bkbKegiatan);
    }

    public function delete(User $user, BkbKegiatan $bkbKegiatan): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'bkb_kegiatan.delete')) {
            return false;
        }

        return $this->view($user, $bkbKegiatan);
    }
}
