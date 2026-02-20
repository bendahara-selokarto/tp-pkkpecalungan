<?php

namespace App\Policies;

use App\Domains\Wilayah\TamanBacaan\Models\TamanBacaan;
use App\Domains\Wilayah\TamanBacaan\Services\TamanBacaanScopeService;
use App\Models\User;

class TamanBacaanPolicy
{
    public function __construct(
        private readonly TamanBacaanScopeService $tamanBacaanScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return $this->tamanBacaanScopeService->canEnterModule($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function view(User $user, TamanBacaan $tamanBacaan): bool
    {
        return $this->tamanBacaanScopeService->canView($user, $tamanBacaan);
    }

    public function update(User $user, TamanBacaan $tamanBacaan): bool
    {
        return $this->tamanBacaanScopeService->canUpdate($user, $tamanBacaan);
    }

    public function delete(User $user, TamanBacaan $tamanBacaan): bool
    {
        return $this->view($user, $tamanBacaan);
    }
}

