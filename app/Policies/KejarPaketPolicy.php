<?php

namespace App\Policies;

use App\Domains\Wilayah\KejarPaket\Models\KejarPaket;
use App\Domains\Wilayah\KejarPaket\Services\KejarPaketScopeService;
use App\Models\User;

class KejarPaketPolicy
{
    public function __construct(
        private readonly KejarPaketScopeService $kejarPaketScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return $this->kejarPaketScopeService->canEnterModule($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function view(User $user, KejarPaket $kejarPaket): bool
    {
        return $this->kejarPaketScopeService->canView($user, $kejarPaket);
    }

    public function update(User $user, KejarPaket $kejarPaket): bool
    {
        return $this->kejarPaketScopeService->canUpdate($user, $kejarPaket);
    }

    public function delete(User $user, KejarPaket $kejarPaket): bool
    {
        return $this->view($user, $kejarPaket);
    }
}


