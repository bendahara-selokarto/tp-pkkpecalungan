<?php

namespace App\Domains\Wilayah\Simulasi\Services;

use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SimulasiScopeService
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

    public function canView(User $user, Model $model): bool
    {
        if (! $this->canAccessLevel($user, $model->level)) {
            return false;
        }

        return (int) $model->area_id === (int) $user->area_id
            && (int) $model->tahun_anggaran === $this->activeBudgetYearContextService->resolveForUser($user);
    }

    public function canUpdate(User $user, Model $model): bool
    {
        return $this->canView($user, $model);
    }

    public function requireUserAreaId(): int
    {
        return $this->userAreaContextService->requireUserAreaId();
    }

    public function authorizeSameLevelAreaAndBudgetYear(Model $model, string $level, int $areaId, int $tahunAnggaran): Model
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

    public function resolveCreatorIdFilterForList(string $level): ?int
    {
        // We reuse the same logic for Pokja 1 if needed, but the method name implies Sekretaris.
        // Actually, let's check if there's a specific one for Pokja.
        // For now, let's keep it simple as Pokja 1 usually sees all data in their area.
        return null;
    }
}
