<?php

namespace App\Domains\Wilayah\BukuTamu\Actions;

use App\Domains\Wilayah\BukuTamu\DTOs\BukuTamuData;
use App\Domains\Wilayah\BukuTamu\Models\BukuTamu;
use App\Domains\Wilayah\BukuTamu\Repositories\BukuTamuRepositoryInterface;
use App\Domains\Wilayah\BukuTamu\Services\BukuTamuScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class CreateScopedBukuTamuAction
{
    public function __construct(
        private readonly BukuTamuRepositoryInterface $bukuTamuRepository,
        private readonly BukuTamuScopeService $bukuTamuScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(array $payload, string $level): BukuTamu
    {
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();

        $fileInfo = [];
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
        }

        $data = BukuTamuData::fromArray([
            'title' => $payload['title'] ?? null,
            'visit_date' => $payload['visit_date'] ?? now()->format('Y-m-d'),
            'guest_name' => $payload['guest_name'] ?? null,
            'purpose' => $payload['purpose'] ?? null,
            'institution' => $payload['institution'] ?? null,
            'description' => $payload['description'] ?? null,
            ...$fileInfo,
            'level' => $level,
            'area_id' => $this->bukuTamuScopeService->requireUserAreaId(),
            'created_by' => auth()->id(),
            'tahun_anggaran' => $tahunAnggaran,
        ]);

        return $this->bukuTamuRepository->store($data);
    }
}
