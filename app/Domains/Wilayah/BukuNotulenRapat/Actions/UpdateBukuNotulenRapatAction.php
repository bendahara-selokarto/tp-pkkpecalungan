<?php

namespace App\Domains\Wilayah\BukuNotulenRapat\Actions;

use App\Domains\Wilayah\BukuNotulenRapat\DTOs\BukuNotulenRapatData;
use App\Domains\Wilayah\BukuNotulenRapat\Models\BukuNotulenRapat;
use App\Domains\Wilayah\BukuNotulenRapat\Repositories\BukuNotulenRapatRepositoryInterface;

class UpdateBukuNotulenRapatAction
{
    public function __construct(
        private readonly BukuNotulenRapatRepositoryInterface $bukuNotulenRapatRepository
    ) {
    }

    public function execute(BukuNotulenRapat $bukuNotulenRapat, array $payload): BukuNotulenRapat
    {
        $fileInfo = [
            'file_path' => $bukuNotulenRapat->file_path,
            'original_name' => $bukuNotulenRapat->original_name,
            'mime_type' => $bukuNotulenRapat->mime_type,
            'extension' => $bukuNotulenRapat->extension,
            'size_bytes' => (int) $bukuNotulenRapat->size_bytes,
        ];

        if (isset($payload['file']) && $payload['file'] instanceof \Illuminate\Http\UploadedFile) {
            $uploadedFile = $payload['file'];
            $storedPath = $uploadedFile->store('buku-notulen', 'public');
            $fileInfo = [
                'file_path' => $storedPath,
                'original_name' => $uploadedFile->getClientOriginalName(),
                'mime_type' => $uploadedFile->getClientMimeType(),
                'extension' => strtolower($uploadedFile->getClientOriginalExtension()),
                'size_bytes' => (int) $uploadedFile->getSize(),
            ];

            // Optional: delete old file
            if ($bukuNotulenRapat->file_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($bukuNotulenRapat->file_path);
            }
        }

        $data = BukuNotulenRapatData::fromArray([
            'entry_date' => $payload['entry_date'] ?? null,
            'title' => $payload['title'] ?? null,
            'person_name' => $payload['person_name'] ?? null,
            'institution' => $payload['institution'] ?? null,
            'description' => $payload['description'] ?? null,
            ...$fileInfo,
            'level' => $bukuNotulenRapat->level,
            'area_id' => $bukuNotulenRapat->area_id,
            'created_by' => $bukuNotulenRapat->created_by,
            'tahun_anggaran' => $bukuNotulenRapat->tahun_anggaran,
        ]);

        return $this->bukuNotulenRapatRepository->update($bukuNotulenRapat, $data);
    }
}
