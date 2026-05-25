<?php

namespace App\Domains\Wilayah\AgendaSuratTugas\Services;

use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Domains\Wilayah\AgendaSuratTugas\Models\AgendaSuratTugas;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AgendaSuratTugasScopeService
{
    public function __construct(
        private readonly UserAreaContextService $userAreaContextService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function requireUserAreaId(?User $user = null): int
    {
        $user = $user ?? auth()->user();

        if (! $user || ! $user->area_id) {
            throw new \RuntimeException('User area context required for this operation.');
        }

        return (int) $user->area_id;
    }

    public function authorizeSameLevelAreaAndBudgetYear(AgendaSuratTugas $model, string $level, int $areaId, int $tahunAnggaran): AgendaSuratTugas
    {
        if (
            $model->level !== $level
            || (int) $model->area_id !== $areaId
            || (int) $model->tahun_anggaran !== $tahunAnggaran
        ) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $model;
    }
}
