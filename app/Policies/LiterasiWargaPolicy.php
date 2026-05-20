<?php

namespace App\Policies;

use App\Support\RoleScopeMatrix;

use App\Domains\Wilayah\LiterasiWarga\Models\LiterasiWarga;
use App\Domains\Wilayah\LiterasiWarga\Services\LiterasiWargaScopeService;
use App\Models\User;

class LiterasiWargaPolicy
{
    use \Illuminate\Auth\Access\HandlesAuthorization;

    public function __construct(
        private readonly LiterasiWargaScopeService $literasiWargaScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'literasi_warga.view');
    }

    public function create(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'literasi_warga.create');
    }

    public function view(User $user, LiterasiWarga $literasiWarga): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'literasi_warga.view')) {
            return false;
        }

        return $this->literasiWargaScopeService->canView($user, $literasiWarga);
    }

    public function update(User $user, LiterasiWarga $literasiWarga): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'literasi_warga.update')) {
            return false;
        }

        return $this->literasiWargaScopeService->canUpdate($user, $literasiWarga);
    }

    public function delete(User $user, LiterasiWarga $literasiWarga): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'literasi_warga.delete')) {
            return false;
        }

        return $this->view($user, $literasiWarga);
    }
}
