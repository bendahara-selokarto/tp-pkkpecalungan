<?php

namespace App\Domains\Wilayah\Activities\UseCases;

use App\Domains\Wilayah\Activities\Repositories\ActivityRepositoryInterface;
use App\Domains\Wilayah\Activities\Services\ActivityScopeService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListKecamatanDesaActivitiesUseCase
{
    public function __construct(
        private readonly ActivityRepositoryInterface $activityRepository,
        private readonly ActivityScopeService $activityScopeService
    ) {
    }

    public function execute(int $perPage): LengthAwarePaginator
    {
        $kecamatanAreaId = $this->activityScopeService->requireUserAreaId();

        return $this->activityRepository->paginateDesaActivitiesByKecamatan($kecamatanAreaId, $perPage);
    }
}
