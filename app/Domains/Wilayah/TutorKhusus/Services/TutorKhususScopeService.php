<?php

namespace App\Domains\Wilayah\TutorKhusus\Services;

use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Domains\Wilayah\TutorKhusus\Models\TutorKhusus;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TutorKhususScopeService
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

    public function canView(User $user, TutorKhusus $tutorKhusus): bool
    {
        if (! $this->canAccessLevel($user, $tutorKhusus->level)) {
            return false;
        }

        return (int) $tutorKhusus->area_id === (int) $user->area_id
            && (int) $tutorKhusus->tahun_anggaran === $this->activeBudgetYearContextService->resolveForUser($user);
    }

    public function canUpdate(User $user, TutorKhusus $tutorKhusus): bool
    {
        return $this->canView($user, $tutorKhusus);
    }

    public function requireUserAreaId(): int
    {
        return $this->userAreaContextService->requireUserAreaId();
    }

    public function authorizeSameLevelAreaAndBudgetYear(TutorKhusus $tutorKhusus, string $level, int $areaId, int $tahunAnggaran): TutorKhusus
    {
        if (
            $tutorKhusus->level !== $level
            || (int) $tutorKhusus->area_id !== $areaId
            || (int) $tutorKhusus->tahun_anggaran !== $tahunAnggaran
        ) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $tutorKhusus;
    }
}
