<?php

namespace App\Policies;

use App\Domains\Wilayah\LiterasiWarga\Models\LiterasiWarga;
use App\Domains\Wilayah\LiterasiWarga\Services\LiterasiWargaScopeService;
use App\Models\User;

class LiterasiWargaPolicy
{
    public function __construct(
        private readonly LiterasiWargaScopeService $literasiWargaScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return $this->literasiWargaScopeService->canEnterModule($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function view(User $user, LiterasiWarga $literasiWarga): bool
    {
        return $this->literasiWargaScopeService->canView($user, $literasiWarga);
    }

    public function update(User $user, LiterasiWarga $literasiWarga): bool
    {
        return $this->literasiWargaScopeService->canUpdate($user, $literasiWarga);
    }

    public function delete(User $user, LiterasiWarga $literasiWarga): bool
    {
        return $this->view($user, $literasiWarga);
    }
}
