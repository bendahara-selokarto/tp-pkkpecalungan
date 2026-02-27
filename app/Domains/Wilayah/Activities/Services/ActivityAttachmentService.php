<?php

namespace App\Domains\Wilayah\Activities\Services;

use App\Domains\Wilayah\Activities\Models\Activity;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ActivityAttachmentService
{
    /**
     * @return array{image_path: string|null, document_path: string|null}
     */
    public function storeFromPayload(array $payload, string $level, int $areaId): array
    {
        return [
            'image_path' => $this->storeFile($payload['image_upload'] ?? null, $level, $areaId, 'images'),
            'document_path' => $this->storeFile($payload['document_upload'] ?? null, $level, $areaId, 'documents'),
        ];
    }

    /**
     * @return array{image_path: string|null, document_path: string|null}
     */
    public function replaceFromPayload(Activity $activity, array $payload): array
    {
        $imagePath = $activity->image_path;
        if (($payload['image_upload'] ?? null) instanceof UploadedFile) {
            $this->deletePath($imagePath);
            $imagePath = $this->storeFile($payload['image_upload'], $activity->level, $activity->area_id, 'images');
        }

        $documentPath = $activity->document_path;
        if (($payload['document_upload'] ?? null) instanceof UploadedFile) {
            $this->deletePath($documentPath);
            $documentPath = $this->storeFile($payload['document_upload'], $activity->level, $activity->area_id, 'documents');
        }

        return [
            'image_path' => $imagePath,
            'document_path' => $documentPath,
        ];
    }

    public function deleteForActivity(Activity $activity): void
    {
        $this->deletePath($activity->image_path);
        $this->deletePath($activity->document_path);
    }

    private function storeFile(mixed $file, string $level, int $areaId, string $subDirectory): ?string
    {
        if (! $file instanceof UploadedFile) {
            return null;
        }

        return $file->store(
            sprintf('activities/%s/%d/%s', $level, $areaId, $subDirectory),
            'public'
        );
    }

    private function deletePath(?string $path): void
    {
        if (! is_string($path) || $path === '') {
            return;
        }

        Storage::disk('public')->delete($path);
    }
}
