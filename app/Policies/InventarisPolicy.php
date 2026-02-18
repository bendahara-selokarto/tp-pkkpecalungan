<?php

namespace App\Policies;

use App\Domains\Wilayah\Inventaris\Models\Inventaris;
use App\Domains\Wilayah\Inventaris\Services\InventarisScopeService;
use App\Models\User;

class InventarisPolicy
{
    public function __construct(
        private readonly InventarisScopeService $inventarisScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return $this->inventarisScopeService->canEnterModule($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function view(User $user, Inventaris $inventaris): bool
    {
        return $this->inventarisScopeService->canView($user, $inventaris);
    }

    public function update(User $user, Inventaris $inventaris): bool
    {
        return $this->inventarisScopeService->canUpdate($user, $inventaris);
    }

    public function delete(User $user, Inventaris $inventaris): bool
    {
        return $this->view($user, $inventaris);
    }
}
