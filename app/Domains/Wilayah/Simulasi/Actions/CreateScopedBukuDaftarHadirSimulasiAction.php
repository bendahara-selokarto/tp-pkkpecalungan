<?php

namespace App\Domains\Wilayah\Simulasi\Actions;

use App\Domains\Wilayah\Simulasi\DTOs\BukuDaftarHadirSimulasiData;
use App\Domains\Wilayah\Simulasi\Models\BukuDaftarHadirSimulasi;
use App\Domains\Wilayah\Simulasi\Repositories\BukuDaftarHadirSimulasiRepositoryInterface;
use App\Domains\Wilayah\Simulasi\Services\SimulasiScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class CreateScopedBukuDaftarHadirSimulasiAction
{
    public function __construct(
        private readonly BukuDaftarHadirSimulasiRepositoryInterface $repository,
        private readonly SimulasiScopeService $scopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(array $payload, string $level): BukuDaftarHadirSimulasi
    {
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();

        $fileInfo = [];
        if (isset($payload['file']) && $payload['file'] instanceof \Illuminate\Http\UploadedFile) {
            $uploadedFile = $payload['file'];
            $storedPath = $uploadedFile->store('buku-daftar-hadir-simulasi', 'public');
            $fileInfo = [
                'file_path' => $storedPath,
                'original_name' => $uploadedFile->getClientOriginalName(),
                'mime_type' => $uploadedFile->getClientMimeType(),
                'extension' => strtolower($uploadedFile->getClientOriginalExtension()),
                'size_bytes' => (int) $uploadedFile->getSize(),
            ];
        }

        $data = BukuDaftarHadirSimulasiData::fromArray([
            ...$payload,
            ...$fileInfo,
            'level' => $level,
            'area_id' => $this->scopeService->requireUserAreaId(),
            'created_by' => auth()->id(),
            'tahun_anggaran' => $tahunAnggaran,
        ]);

        return $this->repository->store($data);
    }
}
