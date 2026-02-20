<?php

namespace App\Policies;

use App\Domains\Wilayah\AnggotaTimPenggerak\Models\AnggotaTimPenggerak;
use App\Domains\Wilayah\AnggotaTimPenggerak\Services\AnggotaTimPenggerakScopeService;
use App\Models\User;

class AnggotaTimPenggerakPolicy
{
    public function __construct(
        private readonly AnggotaTimPenggerakScopeService $anggotaTimPenggerakScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return $this->anggotaTimPenggerakScopeService->canEnterModule($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function view(User $user, AnggotaTimPenggerak $anggotaTimPenggerak): bool
    {
        return $this->anggotaTimPenggerakScopeService->canView($user, $anggotaTimPenggerak);
    }

    public function update(User $user, AnggotaTimPenggerak $anggotaTimPenggerak): bool
    {
        return $this->anggotaTimPenggerakScopeService->canUpdate($user, $anggotaTimPenggerak);
    }

    public function delete(User $user, AnggotaTimPenggerak $anggotaTimPenggerak): bool
    {
        return $this->view($user, $anggotaTimPenggerak);
    }
}
