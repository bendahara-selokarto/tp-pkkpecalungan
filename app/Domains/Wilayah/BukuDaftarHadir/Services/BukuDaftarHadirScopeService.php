<?php

namespace App\Domains\Wilayah\BukuDaftarHadir\Services;

use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\BukuDaftarHadir\Models\BukuDaftarHadir;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class BukuDaftarHadirScopeService
{
    public function __construct(
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService,
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

    public function canView(User $user, BukuDaftarHadir $bukuDaftarHadir): bool
    {
        if (! $this->canAccessLevel($user, $bukuDaftarHadir->level)) {
            return false;
        }

        return (int) $bukuDaftarHadir->area_id === (int) $user->area_id
            && (int) $bukuDaftarHadir->tahun_anggaran === $this->activeBudgetYearContextService->resolveForUser($user);
    }

    public function canUpdate(User $user, BukuDaftarHadir $bukuDaftarHadir): bool
    {
        return $this->canView($user, $bukuDaftarHadir);
    }

    public function requireUserAreaId(): int
    {
        return $this->userAreaContextService->requireUserAreaId();
    }

    public function authorizeSameLevelAreaAndBudgetYear(BukuDaftarHadir $bukuDaftarHadir, string $level, int $areaId, int $tahunAnggaran): BukuDaftarHadir
    {
        if (
            $bukuDaftarHadir->level !== $level
            || (int) $bukuDaftarHadir->area_id !== $areaId
            || (int) $bukuDaftarHadir->tahun_anggaran !== $tahunAnggaran
        ) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $bukuDaftarHadir;
    }

    public function authorizeActivityScope(int $activityId, string $level, int $areaId, int $tahunAnggaran): Activity
    {
        $activity = Activity::query()->findOrFail($activityId);
        $activityYear = $activity->activity_date
            ? (int) date('Y', strtotime((string) $activity->activity_date))
            : null;

        if (
            $activity->level !== $level
            || (int) $activity->area_id !== $areaId
            || $activityYear !== $tahunAnggaran
        ) {
            throw new HttpException(403, 'Kegiatan tidak berada pada scope wilayah Anda.');
        }

        return $activity;
    }

    public function requireActiveBudgetYear(): int
    {
        return $this->activeBudgetYearContextService->requireForAuthenticatedUser();
    }
    public function resolveCreatorIdFilterForList(string $level): ?int
    {
        return $this->userAreaContextService->resolveCreatorIdFilterForKecamatanSekretaris($level);
    }
}
