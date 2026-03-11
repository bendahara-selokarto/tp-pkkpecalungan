<?php

namespace App\Policies;

use App\Domains\Wilayah\PraKoperasiUp2k\Models\PraKoperasiUp2k;
use App\Domains\Wilayah\PraKoperasiUp2k\Services\PraKoperasiUp2kScopeService;
use App\Models\User;

class PraKoperasiUp2kPolicy
{
    public function __construct(
        private readonly PraKoperasiUp2kScopeService $praKoperasiUp2kScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return $this->praKoperasiUp2kScopeService->canEnterModule($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function view(User $user, PraKoperasiUp2k $praKoperasiUp2k): bool
    {
        return $this->praKoperasiUp2kScopeService->canView($user, $praKoperasiUp2k);
    }

    public function update(User $user, PraKoperasiUp2k $praKoperasiUp2k): bool
    {
        return $this->praKoperasiUp2kScopeService->canUpdate($user, $praKoperasiUp2k);
    }

    public function delete(User $user, PraKoperasiUp2k $praKoperasiUp2k): bool
    {
        return $this->view($user, $praKoperasiUp2k);
    }
}
