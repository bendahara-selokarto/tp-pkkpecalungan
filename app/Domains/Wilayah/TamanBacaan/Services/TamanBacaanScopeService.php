<?php

namespace App\Domains\Wilayah\TamanBacaan\Services;

use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use App\Domains\Wilayah\TamanBacaan\Models\TamanBacaan;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TamanBacaanScopeService
{
    public function __construct(
        private readonly UserAreaContextService $userAreaContextService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
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

    public function canView(User $user, TamanBacaan $tamanBacaan): bool
    {
        if (! $this->canAccessLevel($user, $tamanBacaan->level)) {
            return false;
        }

        return (int) $tamanBacaan->area_id === (int) $user->area_id
            && (int) $tamanBacaan->tahun_anggaran === $this->activeBudgetYearContextService->resolveForUser($user);
    }

    public function canUpdate(User $user, TamanBacaan $tamanBacaan): bool
    {
        return $this->canView($user, $tamanBacaan);
    }

    public function requireUserAreaId(): int
    {
        return $this->userAreaContextService->requireUserAreaId();
    }

    public function authorizeSameLevelAreaAndBudgetYear(TamanBacaan $tamanBacaan, string $level, int $areaId, int $tahunAnggaran): TamanBacaan
    {
        if (
            $tamanBacaan->level !== $level
            || (int) $tamanBacaan->area_id !== $areaId
            || (int) $tamanBacaan->tahun_anggaran !== $tahunAnggaran
        ) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $tamanBacaan;
    }
}

