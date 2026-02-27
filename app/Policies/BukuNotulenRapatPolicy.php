<?php

namespace App\Policies;

use App\Domains\Wilayah\BukuNotulenRapat\Models\BukuNotulenRapat;
use App\Domains\Wilayah\BukuNotulenRapat\Services\BukuNotulenRapatScopeService;
use App\Models\User;

class BukuNotulenRapatPolicy
{
    public function __construct(
        private readonly BukuNotulenRapatScopeService $bukuNotulenRapatScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return $this->bukuNotulenRapatScopeService->canEnterModule($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function view(User $user, BukuNotulenRapat $bukuNotulenRapat): bool
    {
        return $this->bukuNotulenRapatScopeService->canView($user, $bukuNotulenRapat);
    }

    public function update(User $user, BukuNotulenRapat $bukuNotulenRapat): bool
    {
        return $this->bukuNotulenRapatScopeService->canUpdate($user, $bukuNotulenRapat);
    }

    public function delete(User $user, BukuNotulenRapat $bukuNotulenRapat): bool
    {
        return $this->view($user, $bukuNotulenRapat);
    }
}
