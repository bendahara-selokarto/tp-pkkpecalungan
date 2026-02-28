<?php

namespace App\Domains\Wilayah\AgendaSurat\Actions;

use App\Domains\Wilayah\AgendaSurat\DTOs\AgendaSuratData;
use App\Domains\Wilayah\AgendaSurat\Models\AgendaSurat;
use App\Domains\Wilayah\AgendaSurat\Repositories\AgendaSuratRepositoryInterface;
use App\Domains\Wilayah\AgendaSurat\Services\AgendaSuratAttachmentService;
use App\Domains\Wilayah\AgendaSurat\Services\AgendaSuratScopeService;

class CreateScopedAgendaSuratAction
{
    public function __construct(
        private readonly AgendaSuratRepositoryInterface $agendaSuratRepository,
        private readonly AgendaSuratScopeService $agendaSuratScopeService,
        private readonly AgendaSuratAttachmentService $agendaSuratAttachmentService
    ) {
    }

    public function execute(array $payload, string $level): AgendaSurat
    {
        $areaId = $this->agendaSuratScopeService->requireUserAreaId();
        $dataDukungPath = $this->agendaSuratAttachmentService->storeFromPayload($payload, $level, $areaId);

        $data = AgendaSuratData::fromArray([
            'jenis_surat' => $payload['jenis_surat'],
            'tanggal_terima' => $payload['tanggal_terima'] ?? null,
            'tanggal_surat' => $payload['tanggal_surat'],
            'nomor_surat' => $payload['nomor_surat'],
            'asal_surat' => $payload['asal_surat'] ?? null,
            'dari' => $payload['dari'] ?? null,
            'kepada' => $payload['kepada'] ?? null,
            'perihal' => $payload['perihal'],
            'lampiran' => $payload['lampiran'] ?? null,
            'diteruskan_kepada' => $payload['diteruskan_kepada'] ?? null,
            'tembusan' => $payload['tembusan'] ?? null,
            'keterangan' => $payload['keterangan'] ?? null,
            'data_dukung_path' => $dataDukungPath,
            'level' => $level,
            'area_id' => $areaId,
            'created_by' => auth()->id(),
        ]);

        return $this->agendaSuratRepository->store($data);
    }
}
