<?php

namespace App\Domains\Wilayah\Koperasi\Services;

use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use App\Domains\Wilayah\Koperasi\Models\Koperasi;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class KoperasiScopeService
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

    public function canView(User $user, Koperasi $koperasi): bool
    {
        if (! $this->canAccessLevel($user, $koperasi->level)) {
            return false;
        }

        return (int) $koperasi->area_id === (int) $user->area_id
            && (int) $koperasi->tahun_anggaran === $this->activeBudgetYearContextService->resolveForUser($user);
    }

    public function canUpdate(User $user, Koperasi $koperasi): bool
    {
        return $this->canView($user, $koperasi);
    }

    public function requireUserAreaId(): int
    {
        return $this->userAreaContextService->requireUserAreaId();
    }

    public function authorizeSameLevelAreaAndBudgetYear(Koperasi $koperasi, string $level, int $areaId, int $tahunAnggaran): Koperasi
    {
        if (
            $koperasi->level !== $level
            || (int) $koperasi->area_id !== $areaId
            || (int) $koperasi->tahun_anggaran !== $tahunAnggaran
        ) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $koperasi;
    }
}


