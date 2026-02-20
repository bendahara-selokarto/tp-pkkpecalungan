<?php

namespace App\Policies;

use App\Domains\Wilayah\Posyandu\Models\Posyandu;
use App\Domains\Wilayah\Posyandu\Services\PosyanduScopeService;
use App\Models\User;

class PosyanduPolicy
{
    public function __construct(
        private readonly PosyanduScopeService $posyanduScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return $this->posyanduScopeService->canEnterModule($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function view(User $user, Posyandu $posyandu): bool
    {
        return $this->posyanduScopeService->canView($user, $posyandu);
    }

    public function update(User $user, Posyandu $posyandu): bool
    {
        return $this->posyanduScopeService->canUpdate($user, $posyandu);
    }

    public function delete(User $user, Posyandu $posyandu): bool
    {
        return $this->view($user, $posyandu);
    }
}


