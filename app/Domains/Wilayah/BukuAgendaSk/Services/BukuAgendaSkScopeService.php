<?php

namespace App\Domains\Wilayah\BukuAgendaSk\Services;

use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Domains\Wilayah\BukuAgendaSk\Models\BukuAgendaSk;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class BukuAgendaSkScopeService
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

    public function canView(User $user, BukuAgendaSk $bukuAgendaSk): bool
    {
        if (! $this->canAccessLevel($user, $bukuAgendaSk->level)) {
            return false;
        }

        return (int) $bukuAgendaSk->area_id === (int) $user->area_id
            && (int) $bukuAgendaSk->tahun_anggaran === $this->activeBudgetYearContextService->resolveForUser($user);
    }

    public function canUpdate(User $user, BukuAgendaSk $bukuAgendaSk): bool
    {
        return $this->canView($user, $bukuAgendaSk);
    }

    public function requireUserAreaId(?User $user = null): int
    {
        $user = $user ?? auth()->user();

        if (! $user || ! $user->area_id) {
            throw new \RuntimeException('User area context required for this operation.');
        }

        return (int) $user->area_id;
    }

    public function authorizeSameLevelAreaAndBudgetYear(BukuAgendaSk $bukuAgendaSk, string $level, int $areaId, int $tahunAnggaran): BukuAgendaSk
    {
        if (
            $bukuAgendaSk->level !== $level
            || (int) $bukuAgendaSk->area_id !== $areaId
            || (int) $bukuAgendaSk->tahun_anggaran !== $tahunAnggaran
        ) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $bukuAgendaSk;
    }
}
