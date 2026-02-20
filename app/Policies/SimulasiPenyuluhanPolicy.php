<?php

namespace App\Policies;

use App\Domains\Wilayah\SimulasiPenyuluhan\Models\SimulasiPenyuluhan;
use App\Domains\Wilayah\SimulasiPenyuluhan\Services\SimulasiPenyuluhanScopeService;
use App\Models\User;

class SimulasiPenyuluhanPolicy
{
    public function __construct(
        private readonly SimulasiPenyuluhanScopeService $simulasiPenyuluhanScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return $this->simulasiPenyuluhanScopeService->canEnterModule($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function view(User $user, SimulasiPenyuluhan $simulasiPenyuluhan): bool
    {
        return $this->simulasiPenyuluhanScopeService->canView($user, $simulasiPenyuluhan);
    }

    public function update(User $user, SimulasiPenyuluhan $simulasiPenyuluhan): bool
    {
        return $this->simulasiPenyuluhanScopeService->canUpdate($user, $simulasiPenyuluhan);
    }

    public function delete(User $user, SimulasiPenyuluhan $simulasiPenyuluhan): bool
    {
        return $this->view($user, $simulasiPenyuluhan);
    }
}
