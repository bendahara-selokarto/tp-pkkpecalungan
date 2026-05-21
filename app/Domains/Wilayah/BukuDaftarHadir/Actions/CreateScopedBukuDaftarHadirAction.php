<?php

namespace App\Domains\Wilayah\BukuDaftarHadir\Actions;

use App\Domains\Wilayah\BukuDaftarHadir\DTOs\BukuDaftarHadirData;
use App\Domains\Wilayah\BukuDaftarHadir\Models\BukuDaftarHadir;
use App\Domains\Wilayah\BukuDaftarHadir\Repositories\BukuDaftarHadirRepositoryInterface;
use App\Domains\Wilayah\BukuDaftarHadir\Services\BukuDaftarHadirScopeService;

class CreateScopedBukuDaftarHadirAction
{
    public function __construct(
        private readonly BukuDaftarHadirRepositoryInterface $bukuDaftarHadirRepository,
        private readonly BukuDaftarHadirScopeService $bukuDaftarHadirScopeService
    ) {
    }

    public function execute(array $payload, string $level): BukuDaftarHadir
    {
        $areaId = $this->bukuDaftarHadirScopeService->requireUserAreaId();
        $tahunAnggaran = $this->bukuDaftarHadirScopeService->requireActiveBudgetYear();

        if (isset($payload['activity_id'])) {
            $this->bukuDaftarHadirScopeService->authorizeActivityScope(
                (int) $payload['activity_id'],
                $level,
                $areaId,
                $tahunAnggaran
            );
        }

        $fileInfo = [];
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
        }

        $data = BukuDaftarHadirData::fromArray([
            'attendance_date' => $payload['attendance_date'],
            'activity_id' => isset($payload['activity_id']) ? (int) $payload['activity_id'] : null,
            'attendee_name' => $payload['attendee_name'] ?? null,
            'institution' => $payload['institution'] ?? null,
            'description' => $payload['description'] ?? null,
            ...$fileInfo,
            'level' => $level,
            'area_id' => $areaId,
            'created_by' => auth()->id(),
            'tahun_anggaran' => $tahunAnggaran,
        ]);

        return $this->bukuDaftarHadirRepository->store($data);
    }
}
