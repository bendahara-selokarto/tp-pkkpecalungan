<?php

namespace App\Domains\Wilayah\LiterasiWarga\Services;

use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Domains\Wilayah\LiterasiWarga\Models\LiterasiWarga;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class LiterasiWargaScopeService
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

    public function canView(User $user, LiterasiWarga $literasiWarga): bool
    {
        if (! $this->canAccessLevel($user, $literasiWarga->level)) {
            return false;
        }

        return (int) $literasiWarga->area_id === (int) $user->area_id
            && (int) $literasiWarga->tahun_anggaran === $this->activeBudgetYearContextService->resolveForUser($user);
    }

    public function canUpdate(User $user, LiterasiWarga $literasiWarga): bool
    {
        return $this->canView($user, $literasiWarga);
    }

    public function requireUserAreaId(): int
    {
        return $this->userAreaContextService->requireUserAreaId();
    }

    public function authorizeSameLevelAreaAndBudgetYear(LiterasiWarga $literasiWarga, string $level, int $areaId, int $tahunAnggaran): LiterasiWarga
    {
        if (
            $literasiWarga->level !== $level
            || (int) $literasiWarga->area_id !== $areaId
            || (int) $literasiWarga->tahun_anggaran !== $tahunAnggaran
        ) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $literasiWarga;
    }
}
