<?php

namespace App\Domains\Wilayah\AgendaSurat\Services;

use App\Domains\Wilayah\AgendaSurat\Models\AgendaSurat;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AgendaSuratAttachmentService
{
    public function storeFromPayload(array $payload, string $level, int $areaId): ?string
    {
        return $this->storeFile($payload['data_dukung_upload'] ?? null, $level, $areaId);
    }

    public function replaceFromPayload(AgendaSurat $agendaSurat, array $payload): ?string
    {
        $currentPath = $agendaSurat->data_dukung_path;

        if (! (($payload['data_dukung_upload'] ?? null) instanceof UploadedFile)) {
            return $currentPath;
        }

        $this->deletePath($currentPath);

        return $this->storeFile($payload['data_dukung_upload'], $agendaSurat->level, $agendaSurat->area_id);
    }

    public function deleteForAgendaSurat(AgendaSurat $agendaSurat): void
    {
        $this->deletePath($agendaSurat->data_dukung_path);
    }

    private function storeFile(mixed $file, string $level, int $areaId): ?string
    {
        if (! $file instanceof UploadedFile) {
            return null;
        }

        return $file->store(
            sprintf('agenda-surat/%s/%d/data-dukung', $level, $areaId),
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
