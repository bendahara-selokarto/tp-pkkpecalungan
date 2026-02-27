<?php

namespace App\Policies;

use App\Domains\Wilayah\BukuTamu\Models\BukuTamu;
use App\Domains\Wilayah\BukuTamu\Services\BukuTamuScopeService;
use App\Models\User;

class BukuTamuPolicy
{
    public function __construct(
        private readonly BukuTamuScopeService $bukuTamuScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return $this->bukuTamuScopeService->canEnterModule($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function view(User $user, BukuTamu $bukuTamu): bool
    {
        return $this->bukuTamuScopeService->canView($user, $bukuTamu);
    }

    public function update(User $user, BukuTamu $bukuTamu): bool
    {
        return $this->bukuTamuScopeService->canUpdate($user, $bukuTamu);
    }

    public function delete(User $user, BukuTamu $bukuTamu): bool
    {
        return $this->view($user, $bukuTamu);
    }
}
