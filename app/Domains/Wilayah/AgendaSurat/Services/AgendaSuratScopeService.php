<?php

namespace App\Domains\Wilayah\AgendaSurat\Services;

use App\Domains\Wilayah\AgendaSurat\Models\AgendaSurat;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AgendaSuratScopeService
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

    public function canView(User $user, AgendaSurat $agendaSurat): bool
    {
        if (! $this->canAccessLevel($user, $agendaSurat->level)) {
            return false;
        }

        return (int) $agendaSurat->area_id === (int) $user->area_id
            && (int) $agendaSurat->tahun_anggaran === $this->activeBudgetYearContextService->resolveForUser($user);
    }

    public function canUpdate(User $user, AgendaSurat $agendaSurat): bool
    {
        return $this->canView($user, $agendaSurat);
    }

    public function requireUserAreaId(): int
    {
        return $this->userAreaContextService->requireUserAreaId();
    }

    public function authorizeSameLevelAreaAndBudgetYear(AgendaSurat $agendaSurat, string $level, int $areaId, int $tahunAnggaran): AgendaSurat
    {
        if (
            $agendaSurat->level !== $level
            || (int) $agendaSurat->area_id !== $areaId
            || (int) $agendaSurat->tahun_anggaran !== $tahunAnggaran
        ) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $agendaSurat;
    }

    public function resolveCreatorIdFilterForList(string $level): ?int
    {
        return $this->userAreaContextService->resolveCreatorIdFilterForKecamatanSekretaris($level);
    }
}
