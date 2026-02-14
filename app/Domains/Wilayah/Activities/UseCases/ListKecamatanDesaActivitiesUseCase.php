<?php

namespace App\Domains\Wilayah\Activities\UseCases;

use App\Domains\Wilayah\Activities\Repositories\ActivityRepository;
use App\Domains\Wilayah\Activities\Services\ActivityScopeService;

class ListKecamatanDesaActivitiesUseCase
{
    public function __construct(
        private readonly ActivityRepository $activityRepository,
        private readonly ActivityScopeService $activityScopeService
    ) {
    }

    public function execute()
    {
        $kecamatanAreaId = $this->activityScopeService->requireUserAreaId();

        return $this->activityRepository->getDesaActivitiesByKecamatan($kecamatanAreaId);
    }
}
