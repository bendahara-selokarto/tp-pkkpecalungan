<?php

namespace App\Domains\Wilayah\AgendaSuratTugas\Actions;

use App\Domains\Wilayah\AgendaSuratTugas\DTOs\AgendaSuratTugasData;
use App\Domains\Wilayah\AgendaSuratTugas\Models\AgendaSuratTugas;
use App\Domains\Wilayah\AgendaSuratTugas\Repositories\AgendaSuratTugasRepository;
use App\Domains\Wilayah\AgendaSuratTugas\Services\AgendaSuratTugasScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class CreateScopedAgendaSuratTugasAction
{
    public function __construct(
        private readonly AgendaSuratTugasRepository $repository,
        private readonly AgendaSuratTugasScopeService $scopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(array $payload, string $level): AgendaSuratTugas
    {
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();

        $fileInfo = [];
        if (isset($payload['file']) && $payload['file'] instanceof \Illuminate\Http\UploadedFile) {
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

        $data = AgendaSuratTugasData::fromArray([
            'nomor_surat' => $payload['nomor_surat'],
            'tanggal_surat' => $payload['tanggal_surat'],
            'kepada' => $payload['kepada'],
            'perihal' => $payload['perihal'],
            'lampiran' => $payload['lampiran'] ?? null,
            'tembusan' => $payload['tembusan'] ?? null,
            ...$fileInfo,
            'level' => $level,
            'area_id' => $this->scopeService->requireUserAreaId(),
            'created_by' => auth()->id(),
            'tahun_anggaran' => $tahunAnggaran,
        ]);

        return $this->repository->create($data);
    }
}
