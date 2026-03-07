<?php

namespace App\Domains\Wilayah\DataKeluarga\Services;

use App\Domains\Wilayah\DataKeluarga\Models\DataKeluarga;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DataKeluargaScopeService
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

    public function canView(User $user, DataKeluarga $dataKeluarga): bool
    {
        if (! $this->canAccessLevel($user, $dataKeluarga->level)) {
            return false;
        }

        return (int) $dataKeluarga->area_id === (int) $user->area_id
            && (int) $dataKeluarga->tahun_anggaran === $this->activeBudgetYearContextService->resolveForUser($user);
    }

    public function canUpdate(User $user, DataKeluarga $dataKeluarga): bool
    {
        return $this->canView($user, $dataKeluarga);
    }

    public function requireUserAreaId(): int
    {
        return $this->userAreaContextService->requireUserAreaId();
    }

    public function authorizeSameLevelAreaAndBudgetYear(DataKeluarga $dataKeluarga, string $level, int $areaId, int $tahunAnggaran): DataKeluarga
    {
        if (
            $dataKeluarga->level !== $level
            || (int) $dataKeluarga->area_id !== $areaId
            || (int) $dataKeluarga->tahun_anggaran !== $tahunAnggaran
        ) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $dataKeluarga;
    }
}
