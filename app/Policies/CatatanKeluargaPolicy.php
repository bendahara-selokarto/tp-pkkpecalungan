<?php

namespace App\Policies;

use App\Support\RoleScopeMatrix;

use App\Domains\Wilayah\CatatanKeluarga\Models\CatatanKeluarga;
use App\Domains\Wilayah\CatatanKeluarga\Services\CatatanKeluargaScopeService;
use App\Models\User;

class CatatanKeluargaPolicy
{
    use \Illuminate\Auth\Access\HandlesAuthorization;

    public function __construct(
        private readonly CatatanKeluargaScopeService $catatanKeluargaScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'catatan_keluarga.view')) {
            return false;
        }

        return $this->catatanKeluargaScopeService->canEnterModule($user);
    }

    public function view(User $user, CatatanKeluarga $catatanKeluarga): bool
    {
        return $this->viewAny($user);
    }
}

