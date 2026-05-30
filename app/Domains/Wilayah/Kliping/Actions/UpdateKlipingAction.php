<?php

namespace App\Domains\Wilayah\Kliping\Actions;

use App\Domains\Wilayah\Kliping\DTOs\KlipingData;
use App\Domains\Wilayah\Kliping\Models\Kliping;
use App\Domains\Wilayah\Kliping\Repositories\KlipingRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class UpdateKlipingAction
{
    public function __construct(
        private readonly KlipingRepositoryInterface $klipingRepository
    ) {
    }

    public function execute(Kliping $kliping, array $payload): Kliping
    {
        $fileInfo = [
            'file_path' => $kliping->file_path,
            'original_name' => $kliping->original_name,
            'mime_type' => $kliping->mime_type,
            'extension' => $kliping->extension,
            'size_bytes' => (int) $kliping->size_bytes,
        ];

        if (isset($payload['file']) && $payload['file'] instanceof \Illuminate\Http\UploadedFile) {
            $uploadedFile = $payload['file'];
            $storedPath = $uploadedFile->store('buku-kliping', 'public');
            $fileInfo = [
                'file_path' => $storedPath,
                'original_name' => $uploadedFile->getClientOriginalName(),
                'mime_type' => $uploadedFile->getClientMimeType(),
                'extension' => strtolower($uploadedFile->getClientOriginalExtension()),
                'size_bytes' => (int) $uploadedFile->getSize(),
            ];

            if ($kliping->file_path) {
                Storage::disk('public')->delete($kliping->file_path);
            }
        }

        $data = KlipingData::fromArray([
            'date' => $payload['date'],
            'description' => $payload['description'],
            ...$fileInfo,
            'level' => $kliping->level,
            'area_id' => $kliping->area_id,
            'created_by' => $kliping->created_by,
            'tahun_anggaran' => $kliping->tahun_anggaran,
        ]);

        return $this->klipingRepository->update($kliping, $data);
    }
}
