<?php

namespace App\Policies;

use App\Domains\Wilayah\DataWarga\Models\DataWarga;
use App\Domains\Wilayah\DataWarga\Services\DataWargaScopeService;
use App\Models\User;

class DataWargaPolicy
{
    public function __construct(
        private readonly DataWargaScopeService $dataWargaScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return $this->dataWargaScopeService->canEnterModule($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function view(User $user, DataWarga $dataWarga): bool
    {
        return $this->dataWargaScopeService->canView($user, $dataWarga);
    }

    public function update(User $user, DataWarga $dataWarga): bool
    {
        return $this->dataWargaScopeService->canUpdate($user, $dataWarga);
    }

    public function delete(User $user, DataWarga $dataWarga): bool
    {
        return $this->view($user, $dataWarga);
    }
}
