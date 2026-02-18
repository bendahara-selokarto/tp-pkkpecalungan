<?php

namespace App\Policies;

use App\Domains\Wilayah\AnggotaPokja\Models\AnggotaPokja;
use App\Domains\Wilayah\AnggotaPokja\Services\AnggotaPokjaScopeService;
use App\Models\User;

class AnggotaPokjaPolicy
{
    public function __construct(
        private readonly AnggotaPokjaScopeService $anggotaPokjaScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return $this->anggotaPokjaScopeService->canEnterModule($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function view(User $user, AnggotaPokja $anggotaPokja): bool
    {
        return $this->anggotaPokjaScopeService->canView($user, $anggotaPokja);
    }

    public function update(User $user, AnggotaPokja $anggotaPokja): bool
    {
        return $this->anggotaPokjaScopeService->canUpdate($user, $anggotaPokja);
    }

    public function delete(User $user, AnggotaPokja $anggotaPokja): bool
    {
        return $this->view($user, $anggotaPokja);
    }
}
