<?php

namespace App\Policies;

use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Models\DataPemanfaatanTanahPekaranganHatinyaPkk;
use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Services\DataPemanfaatanTanahPekaranganHatinyaPkkScopeService;
use App\Models\User;

class DataPemanfaatanTanahPekaranganHatinyaPkkPolicy
{
    public function __construct(
        private readonly DataPemanfaatanTanahPekaranganHatinyaPkkScopeService $dataPemanfaatanTanahPekaranganHatinyaPkkScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return $this->dataPemanfaatanTanahPekaranganHatinyaPkkScopeService->canEnterModule($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function view(User $user, DataPemanfaatanTanahPekaranganHatinyaPkk $dataPemanfaatanTanahPekaranganHatinyaPkk): bool
    {
        return $this->dataPemanfaatanTanahPekaranganHatinyaPkkScopeService->canView($user, $dataPemanfaatanTanahPekaranganHatinyaPkk);
    }

    public function update(User $user, DataPemanfaatanTanahPekaranganHatinyaPkk $dataPemanfaatanTanahPekaranganHatinyaPkk): bool
    {
        return $this->dataPemanfaatanTanahPekaranganHatinyaPkkScopeService->canUpdate($user, $dataPemanfaatanTanahPekaranganHatinyaPkk);
    }

    public function delete(User $user, DataPemanfaatanTanahPekaranganHatinyaPkk $dataPemanfaatanTanahPekaranganHatinyaPkk): bool
    {
        return $this->view($user, $dataPemanfaatanTanahPekaranganHatinyaPkk);
    }
}


