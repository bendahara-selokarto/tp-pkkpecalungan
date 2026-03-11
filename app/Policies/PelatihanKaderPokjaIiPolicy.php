<?php

namespace App\Policies;

use App\Domains\Wilayah\PelatihanKaderPokjaIi\Models\PelatihanKaderPokjaIi;
use App\Domains\Wilayah\PelatihanKaderPokjaIi\Services\PelatihanKaderPokjaIiScopeService;
use App\Models\User;

class PelatihanKaderPokjaIiPolicy
{
    public function __construct(
        private readonly PelatihanKaderPokjaIiScopeService $pelatihanKaderPokjaIiScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return $this->pelatihanKaderPokjaIiScopeService->canEnterModule($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function view(User $user, PelatihanKaderPokjaIi $pelatihanKaderPokjaIi): bool
    {
        return $this->pelatihanKaderPokjaIiScopeService->canView($user, $pelatihanKaderPokjaIi);
    }

    public function update(User $user, PelatihanKaderPokjaIi $pelatihanKaderPokjaIi): bool
    {
        return $this->pelatihanKaderPokjaIiScopeService->canUpdate($user, $pelatihanKaderPokjaIi);
    }

    public function delete(User $user, PelatihanKaderPokjaIi $pelatihanKaderPokjaIi): bool
    {
        return $this->view($user, $pelatihanKaderPokjaIi);
    }
}
