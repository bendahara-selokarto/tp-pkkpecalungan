<?php

namespace App\Domains\Wilayah\PilotProjectNaskahPelaporan\Services;

use App\Domains\Wilayah\PilotProjectNaskahPelaporan\Models\PilotProjectNaskahPelaporanReport;
use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PilotProjectNaskahPelaporanScopeService
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

    public function canView(User $user, PilotProjectNaskahPelaporanReport $report): bool
    {
        if (! $this->canAccessLevel($user, $report->level)) {
            return false;
        }

        return (int) $report->area_id === (int) $user->area_id;
    }

    public function requireUserAreaId(): int
    {
        return $this->userAreaContextService->requireUserAreaId();
    }

    public function authorizeSameLevelAndArea(
        PilotProjectNaskahPelaporanReport $report,
        string $level,
        int $areaId
    ): PilotProjectNaskahPelaporanReport {
        if ($report->level !== $level || (int) $report->area_id !== $areaId) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $report;
    }
}
