<?php

namespace App\Policies;

use App\Domains\Wilayah\DataKeluarga\Models\DataKeluarga;
use App\Domains\Wilayah\DataKeluarga\Services\DataKeluargaScopeService;
use App\Models\User;

class DataKeluargaPolicy
{
    public function __construct(
        private readonly DataKeluargaScopeService $dataKeluargaScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return $this->dataKeluargaScopeService->canEnterModule($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function view(User $user, DataKeluarga $dataKeluarga): bool
    {
        return $this->dataKeluargaScopeService->canView($user, $dataKeluarga);
    }

    public function update(User $user, DataKeluarga $dataKeluarga): bool
    {
        return $this->dataKeluargaScopeService->canUpdate($user, $dataKeluarga);
    }

    public function delete(User $user, DataKeluarga $dataKeluarga): bool
    {
        return $this->view($user, $dataKeluarga);
    }
}

