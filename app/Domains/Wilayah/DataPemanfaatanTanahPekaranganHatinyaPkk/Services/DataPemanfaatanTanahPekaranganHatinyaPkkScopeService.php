<?php

namespace App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Services;

use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Models\DataPemanfaatanTanahPekaranganHatinyaPkk;
use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DataPemanfaatanTanahPekaranganHatinyaPkkScopeService
{
    public function __construct(
        private readonly UserAreaContextService $userAreaContextService
    ) {
    }

    public function canAccessLevel(User $user, string $level): bool
    {
        return $this->userAreaContextService->canAccessLevel($user, $level);
    }

    public function canEnterModule(User $user): bool
    {
        return $this->userAreaContextService->canEnterModule($user);
    }

    public function canView(User $user, DataPemanfaatanTanahPekaranganHatinyaPkk $dataPemanfaatanTanahPekaranganHatinyaPkk): bool
    {
        if (! $this->canAccessLevel($user, $dataPemanfaatanTanahPekaranganHatinyaPkk->level)) {
            return false;
        }

        return (int) $dataPemanfaatanTanahPekaranganHatinyaPkk->area_id === (int) $user->area_id;
    }

    public function canUpdate(User $user, DataPemanfaatanTanahPekaranganHatinyaPkk $dataPemanfaatanTanahPekaranganHatinyaPkk): bool
    {
        return $this->canView($user, $dataPemanfaatanTanahPekaranganHatinyaPkk);
    }

    public function requireUserAreaId(): int
    {
        return $this->userAreaContextService->requireUserAreaId();
    }

    public function authorizeSameLevelAndArea(DataPemanfaatanTanahPekaranganHatinyaPkk $dataPemanfaatanTanahPekaranganHatinyaPkk, string $level, int $areaId): DataPemanfaatanTanahPekaranganHatinyaPkk
    {
        if ($dataPemanfaatanTanahPekaranganHatinyaPkk->level !== $level || (int) $dataPemanfaatanTanahPekaranganHatinyaPkk->area_id !== $areaId) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $dataPemanfaatanTanahPekaranganHatinyaPkk;
    }
}



