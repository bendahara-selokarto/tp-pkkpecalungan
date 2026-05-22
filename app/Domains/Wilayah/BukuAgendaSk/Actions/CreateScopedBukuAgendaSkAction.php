<?php

namespace App\Domains\Wilayah\BukuAgendaSk\Actions;

use App\Domains\Wilayah\BukuAgendaSk\DTOs\BukuAgendaSkData;
use App\Domains\Wilayah\BukuAgendaSk\Models\BukuAgendaSk;
use App\Domains\Wilayah\BukuAgendaSk\Repositories\BukuAgendaSkRepositoryInterface;
use App\Domains\Wilayah\BukuAgendaSk\Services\BukuAgendaSkScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class CreateScopedBukuAgendaSkAction
{
    public function __construct(
        private readonly BukuAgendaSkRepositoryInterface $bukuAgendaSkRepository,
        private readonly BukuAgendaSkScopeService $bukuAgendaSkScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(array $payload, string $level): BukuAgendaSk
    {
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();

        $fileInfo = [];
        if (isset($payload['file']) && $payload['file'] instanceof \Illuminate\Http\UploadedFile) {
            $uploadedFile = $payload['file'];
            $storedPath = $uploadedFile->store('buku-agenda-sk', 'public');
            $fileInfo = [
                'file_path' => $storedPath,
                'original_name' => $uploadedFile->getClientOriginalName(),
                'mime_type' => $uploadedFile->getClientMimeType(),
                'extension' => strtolower($uploadedFile->getClientOriginalExtension()),
                'size_bytes' => (int) $uploadedFile->getSize(),
            ];
        }

        $data = BukuAgendaSkData::fromArray([
            'nomor_sk' => $payload['nomor_sk'],
            'tanggal_sk' => $payload['tanggal_sk'],
            'kepada' => $payload['kepada'],
            'perihal' => $payload['perihal'],
            'tembusan' => $payload['tembusan'] ?? null,
            ...$fileInfo,
            'level' => $level,
            'area_id' => $this->bukuAgendaSkScopeService->requireUserAreaId(),
            'created_by' => auth()->id(),
            'tahun_anggaran' => $tahunAnggaran,
        ]);

        return $this->bukuAgendaSkRepository->store($data);
    }
}
