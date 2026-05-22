<?php

namespace App\Domains\Wilayah\BukuTamu\Actions;

use App\Domains\Wilayah\BukuTamu\DTOs\BukuTamuData;
use App\Domains\Wilayah\BukuTamu\Models\BukuTamu;
use App\Domains\Wilayah\BukuTamu\Repositories\BukuTamuRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class UpdateBukuTamuAction
{
    public function __construct(
        private readonly BukuTamuRepositoryInterface $bukuTamuRepository
    ) {
    }

    public function execute(BukuTamu $bukuTamu, array $payload): BukuTamu
    {
        $fileInfo = [
            'file_path' => $bukuTamu->file_path,
            'original_name' => $bukuTamu->original_name,
            'mime_type' => $bukuTamu->mime_type,
            'extension' => $bukuTamu->extension,
            'size_bytes' => (int) $bukuTamu->size_bytes,
        ];

        if (isset($payload['file']) && $payload['file'] instanceof \Illuminate\Http\UploadedFile) {
            $uploadedFile = $payload['file'];
            $storedPath = $uploadedFile->store('buku-tamu', 'public');
            $fileInfo = [
                'file_path' => $storedPath,
                'original_name' => $uploadedFile->getClientOriginalName(),
                'mime_type' => $uploadedFile->getClientMimeType(),
                'extension' => strtolower($uploadedFile->getClientOriginalExtension()),
                'size_bytes' => (int) $uploadedFile->getSize(),
            ];

            if ($bukuTamu->file_path) {
                Storage::disk('public')->delete($bukuTamu->file_path);
            }
        }

        $data = BukuTamuData::fromArray([
            'title' => $payload['title'] ?? $bukuTamu->title,
            'visit_date' => $payload['visit_date'],
            'guest_name' => $payload['guest_name'] ?? $bukuTamu->guest_name,
            'purpose' => $payload['purpose'] ?? $bukuTamu->purpose,
            'institution' => $payload['institution'] ?? null,
            'description' => $payload['description'] ?? null,
            ...$fileInfo,
            'level' => $bukuTamu->level,
            'area_id' => $bukuTamu->area_id,
            'created_by' => $bukuTamu->created_by,
            'tahun_anggaran' => $bukuTamu->tahun_anggaran,
        ]);

        return $this->bukuTamuRepository->update($bukuTamu, $data);
    }
}
