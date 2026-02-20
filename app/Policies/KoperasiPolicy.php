<?php

namespace App\Policies;

use App\Domains\Wilayah\Koperasi\Models\Koperasi;
use App\Domains\Wilayah\Koperasi\Services\KoperasiScopeService;
use App\Models\User;

class KoperasiPolicy
{
    public function __construct(
        private readonly KoperasiScopeService $koperasiScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return $this->koperasiScopeService->canEnterModule($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function view(User $user, Koperasi $koperasi): bool
    {
        return $this->koperasiScopeService->canView($user, $koperasi);
    }

    public function update(User $user, Koperasi $koperasi): bool
    {
        return $this->koperasiScopeService->canUpdate($user, $koperasi);
    }

    public function delete(User $user, Koperasi $koperasi): bool
    {
        return $this->view($user, $koperasi);
    }
}
