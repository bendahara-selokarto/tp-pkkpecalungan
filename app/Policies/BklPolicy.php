<?php

namespace App\Policies;

use App\Domains\Wilayah\Bkl\Models\Bkl;
use App\Domains\Wilayah\Bkl\Services\BklScopeService;
use App\Models\User;

class BklPolicy
{
    public function __construct(
        private readonly BklScopeService $bklScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return $this->bklScopeService->canEnterModule($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function view(User $user, Bkl $bkl): bool
    {
        return $this->bklScopeService->canView($user, $bkl);
    }

    public function update(User $user, Bkl $bkl): bool
    {
        return $this->bklScopeService->canUpdate($user, $bkl);
    }

    public function delete(User $user, Bkl $bkl): bool
    {
        return $this->view($user, $bkl);
    }
}

