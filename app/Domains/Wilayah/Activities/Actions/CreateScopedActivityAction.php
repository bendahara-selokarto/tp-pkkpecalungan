<?php

namespace App\Domains\Wilayah\Activities\Actions;

use App\Domains\Wilayah\Activities\DTOs\ActivityData;
use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\Activities\Repositories\ActivityRepositoryInterface;
use App\Domains\Wilayah\Activities\Services\ActivityScopeService;

class CreateScopedActivityAction
{
    public function __construct(
        private readonly ActivityRepositoryInterface $activityRepository,
        private readonly ActivityScopeService $activityScopeService
    ) {
    }

    public function execute(array $payload, string $level): Activity
    {
        $data = ActivityData::fromArray([
            'title' => $payload['title'],
            'nama_petugas' => $payload['nama_petugas'] ?? null,
            'jabatan_petugas' => $payload['jabatan_petugas'] ?? null,
            'description' => $payload['description'] ?? $payload['uraian'] ?? null,
            'uraian' => $payload['uraian'] ?? $payload['description'] ?? null,
            'level' => $level,
            'area_id' => $this->activityScopeService->requireUserAreaId(),
            'created_by' => auth()->id(),
            'activity_date' => $payload['activity_date'],
            'tempat_kegiatan' => $payload['tempat_kegiatan'] ?? null,
            'status' => 'draft',
            'tanda_tangan' => $payload['tanda_tangan'] ?? null,
        ]);

        return $this->activityRepository->store($data);
    }
}
