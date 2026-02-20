<?php

namespace App\Policies;

use App\Domains\Wilayah\Bkr\Models\Bkr;
use App\Domains\Wilayah\Bkr\Services\BkrScopeService;
use App\Models\User;

class BkrPolicy
{
    public function __construct(
        private readonly BkrScopeService $bkrScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return $this->bkrScopeService->canEnterModule($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function view(User $user, Bkr $bkr): bool
    {
        return $this->bkrScopeService->canView($user, $bkr);
    }

    public function update(User $user, Bkr $bkr): bool
    {
        return $this->bkrScopeService->canUpdate($user, $bkr);
    }

    public function delete(User $user, Bkr $bkr): bool
    {
        return $this->view($user, $bkr);
    }
}


