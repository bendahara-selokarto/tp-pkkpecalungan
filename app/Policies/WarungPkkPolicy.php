<?php

namespace App\Policies;

use App\Domains\Wilayah\WarungPkk\Models\WarungPkk;
use App\Domains\Wilayah\WarungPkk\Services\WarungPkkScopeService;
use App\Models\User;

class WarungPkkPolicy
{
    public function __construct(
        private readonly WarungPkkScopeService $warungPkkScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return $this->warungPkkScopeService->canEnterModule($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function view(User $user, WarungPkk $warungPkk): bool
    {
        return $this->warungPkkScopeService->canView($user, $warungPkk);
    }

    public function update(User $user, WarungPkk $warungPkk): bool
    {
        return $this->warungPkkScopeService->canUpdate($user, $warungPkk);
    }

    public function delete(User $user, WarungPkk $warungPkk): bool
    {
        return $this->view($user, $warungPkk);
    }
}
