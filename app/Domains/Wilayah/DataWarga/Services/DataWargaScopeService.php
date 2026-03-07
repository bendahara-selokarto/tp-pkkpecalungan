<?php

namespace App\Domains\Wilayah\DataWarga\Services;

use App\Domains\Wilayah\DataWarga\Models\DataWarga;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DataWargaScopeService
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

    public function canView(User $user, DataWarga $dataWarga): bool
    {
        if (! $this->canAccessLevel($user, $dataWarga->level)) {
            return false;
        }

        return (int) $dataWarga->area_id === (int) $user->area_id
            && (int) $dataWarga->tahun_anggaran === $this->activeBudgetYearContextService->resolveForUser($user);
    }

    public function canUpdate(User $user, DataWarga $dataWarga): bool
    {
        return $this->canView($user, $dataWarga);
    }

    public function requireUserAreaId(): int
    {
        return $this->userAreaContextService->requireUserAreaId();
    }

    public function authorizeSameLevelAreaAndBudgetYear(DataWarga $dataWarga, string $level, int $areaId, int $tahunAnggaran): DataWarga
    {
        if (
            $dataWarga->level !== $level
            || (int) $dataWarga->area_id !== $areaId
            || (int) $dataWarga->tahun_anggaran !== $tahunAnggaran
        ) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $dataWarga;
    }
}
