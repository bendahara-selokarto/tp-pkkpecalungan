<?php

namespace App\Policies;

use App\Domains\Wilayah\Paar\Models\Paar;
use App\Domains\Wilayah\Paar\Services\PaarScopeService;
use App\Models\User;

class PaarPolicy
{
    public function __construct(
        private readonly PaarScopeService $paarScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return $this->paarScopeService->canEnterModule($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function view(User $user, Paar $paar): bool
    {
        return $this->paarScopeService->canView($user, $paar);
    }

    public function update(User $user, Paar $paar): bool
    {
        return $this->paarScopeService->canUpdate($user, $paar);
    }

    public function delete(User $user, Paar $paar): bool
    {
        return $this->view($user, $paar);
    }
}