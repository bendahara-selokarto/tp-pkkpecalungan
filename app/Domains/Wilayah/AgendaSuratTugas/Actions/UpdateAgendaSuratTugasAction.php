<?php

namespace App\Domains\Wilayah\AgendaSuratTugas\Actions;

use App\Domains\Wilayah\AgendaSuratTugas\Models\AgendaSuratTugas;
use App\Domains\Wilayah\AgendaSuratTugas\Repositories\AgendaSuratTugasRepository;
use Illuminate\Support\Facades\Storage;

class UpdateAgendaSuratTugasAction
{
    public function __construct(
        private readonly AgendaSuratTugasRepository $repository
    ) {
    }

    public function execute(AgendaSuratTugas $model, array $payload): bool
    {
        $fileInfo = [];
        if (isset($payload['file']) && $payload['file'] instanceof \Illuminate\Http\UploadedFile) {
            // Delete old file
            if ($model->file_path) {
                Storage::disk('public')->delete($model->file_path);
            }

            $uploadedFile = $payload['file'];
            $storedPath = $uploadedFile->store('agenda-surat-tugas', 'public');
            $fileInfo = [
                'file_path' => $storedPath,
                'original_name' => $uploadedFile->getClientOriginalName(),
                'mime_type' => $uploadedFile->getClientMimeType(),
                'extension' => strtolower($uploadedFile->getClientOriginalExtension()),
                'size_bytes' => (int) $uploadedFile->getSize(),
            ];
        }

        $data = [
            'nomor_surat' => $payload['nomor_surat'],
            'tanggal_surat' => $payload['tanggal_surat'],
            'kepada' => $payload['kepada'],
            'perihal' => $payload['perihal'],
            'lampiran' => $payload['lampiran'] ?? null,
            'tembusan' => $payload['tembusan'] ?? null,
            ...$fileInfo,
        ];

        return $this->repository->update($model, $data);
    }
}
