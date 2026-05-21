<?php

namespace App\Domains\Wilayah\BukuEkspedisi\Actions;

use App\Domains\Wilayah\BukuEkspedisi\DTOs\BukuEkspedisiData;
use App\Domains\Wilayah\BukuEkspedisi\Models\BukuEkspedisi;
use App\Domains\Wilayah\BukuEkspedisi\Repositories\BukuEkspedisiRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class UpdateBukuEkspedisiAction
{
    public function __construct(
        private readonly BukuEkspedisiRepositoryInterface $bukuEkspedisiRepository
    ) {
    }

    public function execute(BukuEkspedisi $bukuEkspedisi, array $payload): BukuEkspedisi
    {
        $fileInfo = [
            'file_path' => $bukuEkspedisi->file_path,
            'original_name' => $bukuEkspedisi->original_name,
            'mime_type' => $bukuEkspedisi->mime_type,
            'extension' => $bukuEkspedisi->extension,
            'size_bytes' => (int) $bukuEkspedisi->size_bytes,
        ];

        if (isset($payload['file']) && $payload['file'] instanceof \Illuminate\Http\UploadedFile) {
            $uploadedFile = $payload['file'];
            $storedPath = $uploadedFile->store('buku-ekspedisi', 'public');
            $fileInfo = [
                'file_path' => $storedPath,
                'original_name' => $uploadedFile->getClientOriginalName(),
                'mime_type' => $uploadedFile->getClientMimeType(),
                'extension' => strtolower($uploadedFile->getClientOriginalExtension()),
                'size_bytes' => (int) $uploadedFile->getSize(),
            ];

            if ($bukuEkspedisi->file_path) {
                Storage::disk('public')->delete($bukuEkspedisi->file_path);
            }
        }

        $data = BukuEkspedisiData::fromArray([
            'title' => $payload['title'],
            ...$fileInfo,
            'level' => $bukuEkspedisi->level,
            'area_id' => (int) $bukuEkspedisi->area_id,
            'created_by' => (int) $bukuEkspedisi->created_by,
            'tahun_anggaran' => (int) $bukuEkspedisi->tahun_anggaran,
        ]);

        return $this->bukuEkspedisiRepository->update($bukuEkspedisi, $data);
    }
}
