<?php

namespace App\Domains\Wilayah\Kliping\Actions;

use App\Domains\Wilayah\Kliping\DTOs\KlipingData;
use App\Domains\Wilayah\Kliping\Models\Kliping;
use App\Domains\Wilayah\Kliping\Repositories\KlipingRepositoryInterface;
use App\Domains\Wilayah\Kliping\Services\KlipingScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class CreateScopedKlipingAction
{
    public function __construct(
        private readonly KlipingRepositoryInterface $klipingRepository,
        private readonly KlipingScopeService $klipingScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(array $payload, string $level): Kliping
    {
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();

        $fileInfo = [];
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
        }

        $data = KlipingData::fromArray([
            'date' => $payload['date'],
            'description' => $payload['description'],
            ...$fileInfo,
            'level' => $level,
            'area_id' => $this->klipingScopeService->requireUserAreaId(),
            'created_by' => auth()->id(),
            'tahun_anggaran' => $tahunAnggaran,
        ]);

        return $this->klipingRepository->store($data);
    }
}
