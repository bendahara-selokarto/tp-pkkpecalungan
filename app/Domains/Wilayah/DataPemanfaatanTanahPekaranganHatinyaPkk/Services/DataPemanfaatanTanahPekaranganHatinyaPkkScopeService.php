<?php

namespace App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Services;

use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Models\DataPemanfaatanTanahPekaranganHatinyaPkk;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DataPemanfaatanTanahPekaranganHatinyaPkkScopeService
{
    public function __construct(
        private readonly UserAreaContextService $userAreaContextService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {}

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

        return (int) $dataPemanfaatanTanahPekaranganHatinyaPkk->area_id === (int) $user->area_id
            && (int) $dataPemanfaatanTanahPekaranganHatinyaPkk->tahun_anggaran === $this->activeBudgetYearContextService->resolveForUser($user);
    }

    public function canUpdate(User $user, DataPemanfaatanTanahPekaranganHatinyaPkk $dataPemanfaatanTanahPekaranganHatinyaPkk): bool
    {
        return $this->canView($user, $dataPemanfaatanTanahPekaranganHatinyaPkk);
    }

    public function requireUserAreaId(): int
    {
        return $this->userAreaContextService->requireUserAreaId();
    }

    public function authorizeSameLevelAreaAndBudgetYear(DataPemanfaatanTanahPekaranganHatinyaPkk $dataPemanfaatanTanahPekaranganHatinyaPkk, string $level, int $areaId, int $tahunAnggaran): DataPemanfaatanTanahPekaranganHatinyaPkk
    {
        if (
            $dataPemanfaatanTanahPekaranganHatinyaPkk->level !== $level
            || (int) $dataPemanfaatanTanahPekaranganHatinyaPkk->area_id !== $areaId
            || (int) $dataPemanfaatanTanahPekaranganHatinyaPkk->tahun_anggaran !== $tahunAnggaran
        ) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $dataPemanfaatanTanahPekaranganHatinyaPkk;
    }
}
