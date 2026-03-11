<?php

namespace App\Domains\Wilayah\PelatihanKaderPokjaIi\Services;

use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Domains\Wilayah\PelatihanKaderPokjaIi\Models\PelatihanKaderPokjaIi;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PelatihanKaderPokjaIiScopeService
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

    public function canView(User $user, PelatihanKaderPokjaIi $pelatihanKaderPokjaIi): bool
    {
        if (! $this->canAccessLevel($user, $pelatihanKaderPokjaIi->level)) {
            return false;
        }

        return (int) $pelatihanKaderPokjaIi->area_id === (int) $user->area_id
            && (int) $pelatihanKaderPokjaIi->tahun_anggaran === $this->activeBudgetYearContextService->resolveForUser($user);
    }

    public function canUpdate(User $user, PelatihanKaderPokjaIi $pelatihanKaderPokjaIi): bool
    {
        return $this->canView($user, $pelatihanKaderPokjaIi);
    }

    public function requireUserAreaId(): int
    {
        return $this->userAreaContextService->requireUserAreaId();
    }

    public function authorizeSameLevelAreaAndBudgetYear(PelatihanKaderPokjaIi $pelatihanKaderPokjaIi, string $level, int $areaId, int $tahunAnggaran): PelatihanKaderPokjaIi
    {
        if (
            $pelatihanKaderPokjaIi->level !== $level
            || (int) $pelatihanKaderPokjaIi->area_id !== $areaId
            || (int) $pelatihanKaderPokjaIi->tahun_anggaran !== $tahunAnggaran
        ) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $pelatihanKaderPokjaIi;
    }
}
