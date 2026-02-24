<?php

namespace App\Domains\Wilayah\LaporanTahunanPkk\Services;

use App\Domains\Wilayah\LaporanTahunanPkk\Models\LaporanTahunanPkkReport;
use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class LaporanTahunanPkkScopeService
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

    public function canView(User $user, LaporanTahunanPkkReport $report): bool
    {
        if (! $this->canAccessLevel($user, $report->level)) {
            return false;
        }

        return (int) $report->area_id === (int) $user->area_id;
    }

    public function canUpdate(User $user, LaporanTahunanPkkReport $report): bool
    {
        return $this->canView($user, $report);
    }

    public function requireUserAreaId(): int
    {
        return $this->userAreaContextService->requireUserAreaId();
    }

    public function authorizeSameLevelAndArea(
        LaporanTahunanPkkReport $report,
        string $level,
        int $areaId
    ): LaporanTahunanPkkReport {
        if ($report->level !== $level || (int) $report->area_id !== $areaId) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $report;
    }
}

