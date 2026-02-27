<?php

namespace App\Domains\Wilayah\Activities\Actions;

use App\Domains\Wilayah\Activities\DTOs\ActivityData;
use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\Activities\Repositories\ActivityRepositoryInterface;
use App\Domains\Wilayah\Activities\Services\ActivityAttachmentService;

class UpdateActivityAction
{
    public function __construct(
        private readonly ActivityRepositoryInterface $activityRepository,
        private readonly ActivityAttachmentService $activityAttachmentService
    ) {
    }

    public function execute(Activity $activity, array $payload): Activity
    {
        $attachments = $this->activityAttachmentService->replaceFromPayload($activity, $payload);

        $data = ActivityData::fromArray([
            'title' => $payload['title'],
            'nama_petugas' => $payload['nama_petugas'] ?? null,
            'jabatan_petugas' => $payload['jabatan_petugas'] ?? null,
            'description' => $payload['description'] ?? $payload['uraian'] ?? null,
            'uraian' => $payload['uraian'] ?? $payload['description'] ?? null,
            'level' => $activity->level,
            'area_id' => $activity->area_id,
            'created_by' => $activity->created_by,
            'activity_date' => $payload['activity_date'],
            'tempat_kegiatan' => $payload['tempat_kegiatan'] ?? null,
            'status' => $payload['status'],
            'tanda_tangan' => $payload['tanda_tangan'] ?? null,
            'image_path' => $attachments['image_path'],
            'document_path' => $attachments['document_path'],
        ]);

        return $this->activityRepository->update($activity, $data);
    }
}
