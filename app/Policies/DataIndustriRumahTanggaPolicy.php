<?php

namespace App\Policies;

use App\Domains\Wilayah\DataIndustriRumahTangga\Models\DataIndustriRumahTangga;
use App\Domains\Wilayah\DataIndustriRumahTangga\Services\DataIndustriRumahTanggaScopeService;
use App\Models\User;

class DataIndustriRumahTanggaPolicy
{
    public function __construct(
        private readonly DataIndustriRumahTanggaScopeService $dataIndustriRumahTanggaScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return $this->dataIndustriRumahTanggaScopeService->canEnterModule($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function view(User $user, DataIndustriRumahTangga $dataIndustriRumahTangga): bool
    {
        return $this->dataIndustriRumahTanggaScopeService->canView($user, $dataIndustriRumahTangga);
    }

    public function update(User $user, DataIndustriRumahTangga $dataIndustriRumahTangga): bool
    {
        return $this->dataIndustriRumahTanggaScopeService->canUpdate($user, $dataIndustriRumahTangga);
    }

    public function delete(User $user, DataIndustriRumahTangga $dataIndustriRumahTangga): bool
    {
        return $this->view($user, $dataIndustriRumahTangga);
    }
}



