<?php

namespace App\Domains\Wilayah\PilotProjectNaskahPelaporan\Services;

use App\Domains\Wilayah\PilotProjectNaskahPelaporan\Models\PilotProjectNaskahPelaporanAttachment;
use App\Domains\Wilayah\PilotProjectNaskahPelaporan\Models\PilotProjectNaskahPelaporanReport;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class PilotProjectNaskahPelaporanAttachmentService
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function buildAttachmentRows(
        PilotProjectNaskahPelaporanReport $report,
        array $payload,
        string $level,
        int $areaId,
        int $createdBy
    ): array {
        $rows = [];
        $now = now();

        foreach ($this->fieldToCategoryMap() as $field => $category) {
            $files = $payload[$field] ?? [];
            if (! is_array($files)) {
                continue;
            }

            foreach ($files as $file) {
                if (! $file instanceof UploadedFile) {
                    continue;
                }

                $storedPath = $file->store(
                    sprintf('pilot-project-naskah-pelaporan/%s/%d/%d/%s', $level, $areaId, $report->id, $category),
                    'public'
                );

                $rows[] = [
                    'category' => $category,
                    'file_path' => $storedPath,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => (string) $file->getClientMimeType(),
                    'file_size' => (int) $file->getSize(),
                    'level' => $level,
                    'area_id' => $areaId,
                    'created_by' => $createdBy,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        return $rows;
    }

    public function deleteFiles(Collection $attachments): void
    {
        foreach ($attachments as $attachment) {
            $path = (string) data_get($attachment, 'file_path', '');
            if ($path === '') {
                continue;
            }

            Storage::disk('public')->delete($path);
        }
    }

    /**
     * @return array<string, string>
     */
    private function fieldToCategoryMap(): array
    {
        return [
            'lampiran_6a_foto' => PilotProjectNaskahPelaporanAttachment::CATEGORY_6A_PHOTO,
            'lampiran_6b_foto' => PilotProjectNaskahPelaporanAttachment::CATEGORY_6B_PHOTO,
            'lampiran_6d_dokumen' => PilotProjectNaskahPelaporanAttachment::CATEGORY_6D_DOCUMENT,
            'lampiran_6e_foto' => PilotProjectNaskahPelaporanAttachment::CATEGORY_6E_PHOTO,
        ];
    }
}
