<?php

namespace App\Domains\Wilayah\AnggotaPokja\Services;

use App\Domains\Wilayah\AnggotaPokja\Models\AnggotaPokja;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AnggotaPokjaScopeService
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

    public function canView(User $user, AnggotaPokja $anggotaPokja): bool
    {
        if (! $this->canAccessLevel($user, $anggotaPokja->level)) {
            return false;
        }

        return (int) $anggotaPokja->area_id === (int) $user->area_id
            && (int) $anggotaPokja->tahun_anggaran === $this->activeBudgetYearContextService->resolveForUser($user);
    }

    public function canUpdate(User $user, AnggotaPokja $anggotaPokja): bool
    {
        return $this->canView($user, $anggotaPokja);
    }

    public function requireUserAreaId(): int
    {
        return $this->userAreaContextService->requireUserAreaId();
    }

    public function authorizeSameLevelAreaAndBudgetYear(AnggotaPokja $anggotaPokja, string $level, int $areaId, int $tahunAnggaran): AnggotaPokja
    {
        if (
            $anggotaPokja->level !== $level
            || (int) $anggotaPokja->area_id !== $areaId
            || (int) $anggotaPokja->tahun_anggaran !== $tahunAnggaran
        ) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $anggotaPokja;
    }

    public function resolveCreatorIdFilterForList(string $level): ?int
    {
        return $this->userAreaContextService->resolveCreatorIdFilterForKecamatanSekretaris($level);
    }
}
