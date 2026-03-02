<?php

namespace App\Domains\Wilayah\AgendaSurat\Actions;

use App\Domains\Wilayah\AgendaSurat\DTOs\AgendaSuratData;
use App\Domains\Wilayah\AgendaSurat\Models\AgendaSurat;
use App\Domains\Wilayah\AgendaSurat\Repositories\AgendaSuratRepositoryInterface;
use App\Domains\Wilayah\AgendaSurat\Services\AgendaSuratAttachmentService;

class UpdateAgendaSuratAction
{
    public function __construct(
        private readonly AgendaSuratRepositoryInterface $agendaSuratRepository,
        private readonly AgendaSuratAttachmentService $agendaSuratAttachmentService
    ) {
    }

    public function execute(AgendaSurat $agendaSurat, array $payload): AgendaSurat
    {
        $dataDukungPath = $this->agendaSuratAttachmentService->replaceFromPayload($agendaSurat, $payload);

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
            'level' => $agendaSurat->level,
            'area_id' => $agendaSurat->area_id,
            'created_by' => $agendaSurat->created_by,
        ]);

        return $this->agendaSuratRepository->update($agendaSurat, $data);
    }
}
