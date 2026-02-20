<?php

namespace App\Policies;

use App\Domains\Wilayah\DataKegiatanWarga\Models\DataKegiatanWarga;
use App\Domains\Wilayah\DataKegiatanWarga\Services\DataKegiatanWargaScopeService;
use App\Models\User;

class DataKegiatanWargaPolicy
{
    public function __construct(
        private readonly DataKegiatanWargaScopeService $dataKegiatanWargaScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return $this->dataKegiatanWargaScopeService->canEnterModule($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function view(User $user, DataKegiatanWarga $dataKegiatanWarga): bool
    {
        return $this->dataKegiatanWargaScopeService->canView($user, $dataKegiatanWarga);
    }

    public function update(User $user, DataKegiatanWarga $dataKegiatanWarga): bool
    {
        return $this->dataKegiatanWargaScopeService->canUpdate($user, $dataKegiatanWarga);
    }

    public function delete(User $user, DataKegiatanWarga $dataKegiatanWarga): bool
    {
        return $this->view($user, $dataKegiatanWarga);
    }
}
