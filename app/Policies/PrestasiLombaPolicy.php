<?php

namespace App\Policies;

use App\Domains\Wilayah\PrestasiLomba\Models\PrestasiLomba;
use App\Domains\Wilayah\PrestasiLomba\Services\PrestasiLombaScopeService;
use App\Models\User;

class PrestasiLombaPolicy
{
    public function __construct(
        private readonly PrestasiLombaScopeService $prestasiLombaScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return $this->prestasiLombaScopeService->canEnterModule($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function view(User $user, PrestasiLomba $prestasiLomba): bool
    {
        return $this->prestasiLombaScopeService->canView($user, $prestasiLomba);
    }

    public function update(User $user, PrestasiLomba $prestasiLomba): bool
    {
        return $this->prestasiLombaScopeService->canUpdate($user, $prestasiLomba);
    }

    public function delete(User $user, PrestasiLomba $prestasiLomba): bool
    {
        return $this->view($user, $prestasiLomba);
    }
}
