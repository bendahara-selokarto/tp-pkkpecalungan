<?php

namespace App\Policies;

use App\Domains\Wilayah\DataPelatihanKader\Models\DataPelatihanKader;
use App\Domains\Wilayah\DataPelatihanKader\Services\DataPelatihanKaderScopeService;
use App\Models\User;

class DataPelatihanKaderPolicy
{
    public function __construct(
        private readonly DataPelatihanKaderScopeService $dataPelatihanKaderScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return $this->dataPelatihanKaderScopeService->canEnterModule($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function view(User $user, DataPelatihanKader $dataPelatihanKader): bool
    {
        return $this->dataPelatihanKaderScopeService->canView($user, $dataPelatihanKader);
    }

    public function update(User $user, DataPelatihanKader $dataPelatihanKader): bool
    {
        return $this->dataPelatihanKaderScopeService->canUpdate($user, $dataPelatihanKader);
    }

    public function delete(User $user, DataPelatihanKader $dataPelatihanKader): bool
    {
        return $this->view($user, $dataPelatihanKader);
    }
}
