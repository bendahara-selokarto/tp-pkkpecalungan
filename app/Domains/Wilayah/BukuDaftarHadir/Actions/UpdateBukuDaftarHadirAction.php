<?php

namespace App\Domains\Wilayah\BukuDaftarHadir\Actions;

use App\Domains\Wilayah\BukuDaftarHadir\DTOs\BukuDaftarHadirData;
use App\Domains\Wilayah\BukuDaftarHadir\Models\BukuDaftarHadir;
use App\Domains\Wilayah\BukuDaftarHadir\Repositories\BukuDaftarHadirRepositoryInterface;
use App\Domains\Wilayah\BukuDaftarHadir\Services\BukuDaftarHadirScopeService;

class UpdateBukuDaftarHadirAction
{
    public function __construct(
        private readonly BukuDaftarHadirRepositoryInterface $bukuDaftarHadirRepository,
        private readonly BukuDaftarHadirScopeService $bukuDaftarHadirScopeService
    ) {
    }

    public function execute(BukuDaftarHadir $bukuDaftarHadir, array $payload): BukuDaftarHadir
    {
        $tahunAnggaran = (int) $bukuDaftarHadir->tahun_anggaran;

        if (isset($payload['activity_id'])) {
            $this->bukuDaftarHadirScopeService->authorizeActivityScope(
                (int) $payload['activity_id'],
                $bukuDaftarHadir->level,
                (int) $bukuDaftarHadir->area_id,
                $tahunAnggaran
            );
        }

        $fileInfo = [
            'file_path' => $bukuDaftarHadir->file_path,
            'original_name' => $bukuDaftarHadir->original_name,
            'mime_type' => $bukuDaftarHadir->mime_type,
            'extension' => $bukuDaftarHadir->extension,
            'size_bytes' => (int) $bukuDaftarHadir->size_bytes,
        ];

        if (isset($payload['file']) && $payload['file'] instanceof \Illuminate\Http\UploadedFile) {
            $uploadedFile = $payload['file'];
            $storedPath = $uploadedFile->store('buku-daftar-hadir', 'public');
            $fileInfo = [
                'file_path' => $storedPath,
                'original_name' => $uploadedFile->getClientOriginalName(),
                'mime_type' => $uploadedFile->getClientMimeType(),
                'extension' => strtolower($uploadedFile->getClientOriginalExtension()),
                'size_bytes' => (int) $uploadedFile->getSize(),
            ];

            if ($bukuDaftarHadir->file_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($bukuDaftarHadir->file_path);
            }
        }

        $data = BukuDaftarHadirData::fromArray([
            'title' => $payload['title'] ?? $bukuDaftarHadir->title,
            'attendance_date' => $payload['attendance_date'],
            'activity_id' => isset($payload['activity_id']) ? (int) $payload['activity_id'] : null,
            'attendee_name' => $payload['attendee_name'] ?? null,
            'institution' => $payload['institution'] ?? null,
            'description' => $payload['description'] ?? null,
            ...$fileInfo,
            'level' => $bukuDaftarHadir->level,
            'area_id' => $bukuDaftarHadir->area_id,
            'created_by' => $bukuDaftarHadir->created_by,
            'tahun_anggaran' => $tahunAnggaran,
        ]);

        return $this->bukuDaftarHadirRepository->update($bukuDaftarHadir, $data);
    }
}
