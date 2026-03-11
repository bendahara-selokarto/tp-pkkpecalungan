<?php

namespace App\Domains\Wilayah\BkbKegiatan\Services;

use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Domains\Wilayah\BkbKegiatan\Models\BkbKegiatan;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class BkbKegiatanScopeService
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

    public function canView(User $user, BkbKegiatan $bkbKegiatan): bool
    {
        if (! $this->canAccessLevel($user, $bkbKegiatan->level)) {
            return false;
        }

        return (int) $bkbKegiatan->area_id === (int) $user->area_id
            && (int) $bkbKegiatan->tahun_anggaran === $this->activeBudgetYearContextService->resolveForUser($user);
    }

    public function canUpdate(User $user, BkbKegiatan $bkbKegiatan): bool
    {
        return $this->canView($user, $bkbKegiatan);
    }

    public function requireUserAreaId(): int
    {
        return $this->userAreaContextService->requireUserAreaId();
    }

    public function authorizeSameLevelAreaAndBudgetYear(BkbKegiatan $bkbKegiatan, string $level, int $areaId, int $tahunAnggaran): BkbKegiatan
    {
        if (
            $bkbKegiatan->level !== $level
            || (int) $bkbKegiatan->area_id !== $areaId
            || (int) $bkbKegiatan->tahun_anggaran !== $tahunAnggaran
        ) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $bkbKegiatan;
    }
}
