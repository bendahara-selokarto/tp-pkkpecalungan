<?php

namespace App\Domains\Wilayah\BukuDaftarHadir\Services;

use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\BukuDaftarHadir\Models\BukuDaftarHadir;
use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class BukuDaftarHadirScopeService
{
    public function __construct(
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

        return (int) $bukuDaftarHadir->area_id === (int) $user->area_id;
    }

    public function canUpdate(User $user, BukuDaftarHadir $bukuDaftarHadir): bool
    {
        return $this->canView($user, $bukuDaftarHadir);
    }

    public function requireUserAreaId(): int
    {
        return $this->userAreaContextService->requireUserAreaId();
    }

    public function authorizeSameLevelAndArea(BukuDaftarHadir $bukuDaftarHadir, string $level, int $areaId): BukuDaftarHadir
    {
        if ($bukuDaftarHadir->level !== $level || (int) $bukuDaftarHadir->area_id !== $areaId) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $bukuDaftarHadir;
    }

    public function authorizeActivityScope(int $activityId, string $level, int $areaId): Activity
    {
        $activity = Activity::query()->findOrFail($activityId);

        if ($activity->level !== $level || (int) $activity->area_id !== $areaId) {
            throw new HttpException(403, 'Kegiatan tidak berada pada scope wilayah Anda.');
        }

        return $activity;
    }
    public function resolveCreatorIdFilterForList(string $level): ?int
    {
        return $this->userAreaContextService->resolveCreatorIdFilterForKecamatanSekretaris($level);
    }
}

