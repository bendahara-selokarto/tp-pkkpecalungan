<?php

namespace App\Domains\Wilayah\Activities\Actions;

use App\Domains\Wilayah\Activities\DTOs\ActivityData;
use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\Activities\Repositories\ActivityRepositoryInterface;
use App\Domains\Wilayah\Activities\Services\ActivityAttachmentService;
use App\Domains\Wilayah\Activities\Services\ActivityScopeService;

class CreateScopedActivityAction
{
    public function __construct(
        private readonly ActivityRepositoryInterface $activityRepository,
        private readonly ActivityScopeService $activityScopeService,
        private readonly ActivityAttachmentService $activityAttachmentService
    ) {
    }

    public function execute(array $payload, string $level): Activity
    {
        $areaId = $this->activityScopeService->requireUserAreaId();
        $attachments = $this->activityAttachmentService->storeFromPayload($payload, $level, $areaId);

        $data = ActivityData::fromArray([
            'title' => $payload['title'],
            'nama_petugas' => $payload['nama_petugas'] ?? null,
            'jabatan_petugas' => $payload['jabatan_petugas'] ?? null,
            'description' => $payload['description'] ?? $payload['uraian'] ?? null,
            'uraian' => $payload['uraian'] ?? $payload['description'] ?? null,
            'level' => $level,
            'area_id' => $areaId,
            'created_by' => auth()->id(),
            'activity_date' => $payload['activity_date'],
            'tempat_kegiatan' => $payload['tempat_kegiatan'] ?? null,
            'status' => 'draft',
            'tanda_tangan' => $payload['tanda_tangan'] ?? null,
            'image_path' => $attachments['image_path'],
            'document_path' => $attachments['document_path'],
        ]);

        return $this->activityRepository->store($data);
    }
}
