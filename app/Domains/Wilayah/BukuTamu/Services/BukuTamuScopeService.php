<?php

namespace App\Domains\Wilayah\BukuTamu\Services;

use App\Domains\Wilayah\BukuTamu\Models\BukuTamu;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class BukuTamuScopeService
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

    public function canView(User $user, BukuTamu $bukuTamu): bool
    {
        if (! $this->canAccessLevel($user, $bukuTamu->level)) {
            return false;
        }

        return (int) $bukuTamu->area_id === (int) $user->area_id
            && (int) $bukuTamu->tahun_anggaran === $this->activeBudgetYearContextService->resolveForUser($user);
    }

    public function canUpdate(User $user, BukuTamu $bukuTamu): bool
    {
        return $this->canView($user, $bukuTamu);
    }

    public function requireUserAreaId(): int
    {
        return $this->userAreaContextService->requireUserAreaId();
    }

    public function authorizeSameLevelAreaAndBudgetYear(BukuTamu $bukuTamu, string $level, int $areaId, int $tahunAnggaran): BukuTamu
    {
        if (
            $bukuTamu->level !== $level
            || (int) $bukuTamu->area_id !== $areaId
            || (int) $bukuTamu->tahun_anggaran !== $tahunAnggaran
        ) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $bukuTamu;
    }
    public function resolveCreatorIdFilterForList(string $level): ?int
    {
        return $this->userAreaContextService->resolveCreatorIdFilterForKecamatanSekretaris($level);
    }
}
