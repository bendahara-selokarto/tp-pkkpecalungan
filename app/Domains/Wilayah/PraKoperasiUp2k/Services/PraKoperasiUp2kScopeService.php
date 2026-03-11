<?php

namespace App\Domains\Wilayah\PraKoperasiUp2k\Services;

use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Domains\Wilayah\PraKoperasiUp2k\Models\PraKoperasiUp2k;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PraKoperasiUp2kScopeService
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

    public function canView(User $user, PraKoperasiUp2k $praKoperasiUp2k): bool
    {
        if (! $this->canAccessLevel($user, $praKoperasiUp2k->level)) {
            return false;
        }

        return (int) $praKoperasiUp2k->area_id === (int) $user->area_id
            && (int) $praKoperasiUp2k->tahun_anggaran === $this->activeBudgetYearContextService->resolveForUser($user);
    }

    public function canUpdate(User $user, PraKoperasiUp2k $praKoperasiUp2k): bool
    {
        return $this->canView($user, $praKoperasiUp2k);
    }

    public function requireUserAreaId(): int
    {
        return $this->userAreaContextService->requireUserAreaId();
    }

    public function authorizeSameLevelAreaAndBudgetYear(PraKoperasiUp2k $praKoperasiUp2k, string $level, int $areaId, int $tahunAnggaran): PraKoperasiUp2k
    {
        if (
            $praKoperasiUp2k->level !== $level
            || (int) $praKoperasiUp2k->area_id !== $areaId
            || (int) $praKoperasiUp2k->tahun_anggaran !== $tahunAnggaran
        ) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $praKoperasiUp2k;
    }
}
