<?php

namespace App\Policies;

use App\Domains\Wilayah\BukuKeuangan\Models\BukuKeuangan;
use App\Domains\Wilayah\BukuKeuangan\Services\BukuKeuanganScopeService;
use App\Models\User;

class BukuKeuanganPolicy
{
    public function __construct(
        private readonly BukuKeuanganScopeService $bukuKeuanganScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return $this->bukuKeuanganScopeService->canEnterModule($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function view(User $user, BukuKeuangan $bukuKeuangan): bool
    {
        return $this->bukuKeuanganScopeService->canView($user, $bukuKeuangan);
    }

    public function update(User $user, BukuKeuangan $bukuKeuangan): bool
    {
        return $this->bukuKeuanganScopeService->canUpdate($user, $bukuKeuangan);
    }

    public function delete(User $user, BukuKeuangan $bukuKeuangan): bool
    {
        return $this->view($user, $bukuKeuangan);
    }
}
