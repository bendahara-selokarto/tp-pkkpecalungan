<?php

namespace App\Domains\Wilayah\PilotProjectNaskahPelaporan\Services;

use App\Domains\Wilayah\PilotProjectNaskahPelaporan\Models\PilotProjectNaskahPelaporanReport;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PilotProjectNaskahPelaporanScopeService
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

    public function canView(User $user, PilotProjectNaskahPelaporanReport $report): bool
    {
        if (! $this->canAccessLevel($user, $report->level)) {
            return false;
        }

        return (int) $report->area_id === (int) $user->area_id
            && (int) $report->tahun_anggaran === $this->activeBudgetYearContextService->resolveForUser($user);
    }

    public function canUpdate(User $user, PilotProjectNaskahPelaporanReport $report): bool
    {
        return $this->canView($user, $report);
    }

    public function requireUserAreaId(): int
    {
        return $this->userAreaContextService->requireUserAreaId();
    }

    public function authorizeSameLevelAreaAndBudgetYear(
        PilotProjectNaskahPelaporanReport $report,
        string $level,
        int $areaId,
        int $tahunAnggaran
    ): PilotProjectNaskahPelaporanReport {
        if (
            $report->level !== $level
            || (int) $report->area_id !== $areaId
            || (int) $report->tahun_anggaran !== $tahunAnggaran
        ) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $report;
    }
}
