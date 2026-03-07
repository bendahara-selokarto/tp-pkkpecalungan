<?php

namespace App\Domains\Wilayah\Activities\UseCases;

use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\Activities\Repositories\ActivityRepositoryInterface;
use App\Domains\Wilayah\Activities\Services\ActivityScopeService;

class GetKecamatanDesaActivityUseCase
{
    public function __construct(
        private readonly ActivityRepositoryInterface $activityRepository,
        private readonly ActivityScopeService $activityScopeService
    ) {}

    public function execute(int $id): Activity
    {
        $activity = $this->activityRepository->find($id)->loadMissing(['area', 'creator']);
        $kecamatanAreaId = $this->activityScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activityScopeService->requireActiveBudgetYear();

        return $this->activityScopeService->authorizeDesaInKecamatanAndBudgetYear($activity, $kecamatanAreaId, $tahunAnggaran);
    }
}
