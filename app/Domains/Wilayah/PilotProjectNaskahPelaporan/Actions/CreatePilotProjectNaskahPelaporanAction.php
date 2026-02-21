<?php

namespace App\Domains\Wilayah\PilotProjectNaskahPelaporan\Actions;

use App\Domains\Wilayah\PilotProjectNaskahPelaporan\Models\PilotProjectNaskahPelaporanReport;
use App\Domains\Wilayah\PilotProjectNaskahPelaporan\Repositories\PilotProjectNaskahPelaporanRepositoryInterface;
use App\Domains\Wilayah\PilotProjectNaskahPelaporan\Services\PilotProjectNaskahPelaporanAttachmentService;
use App\Domains\Wilayah\PilotProjectNaskahPelaporan\Services\PilotProjectNaskahPelaporanScopeService;

class CreatePilotProjectNaskahPelaporanAction
{
    public function __construct(
        private readonly PilotProjectNaskahPelaporanRepositoryInterface $repository,
        private readonly PilotProjectNaskahPelaporanScopeService $scopeService,
        private readonly PilotProjectNaskahPelaporanAttachmentService $attachmentService
    ) {
    }

    public function execute(array $payload, string $level): PilotProjectNaskahPelaporanReport
    {
        $areaId = $this->scopeService->requireUserAreaId();
        $createdBy = (int) auth()->id();

        $report = $this->repository->storeReport([
            'judul_laporan' => (string) ($payload['judul_laporan'] ?? config('pilot_project_naskah_pelaporan.module.label')),
            'surat_kepada' => $this->textOrNull($payload['surat_kepada'] ?? null),
            'surat_dari' => $this->textOrNull($payload['surat_dari'] ?? null),
            'surat_tembusan' => $this->textOrNull($payload['surat_tembusan'] ?? null),
            'surat_tanggal' => $payload['surat_tanggal'] ?? null,
            'surat_nomor' => $this->textOrNull($payload['surat_nomor'] ?? null),
            'surat_sifat' => $this->textOrNull($payload['surat_sifat'] ?? null),
            'surat_lampiran' => $this->textOrNull($payload['surat_lampiran'] ?? null),
            'surat_hal' => $this->textOrNull($payload['surat_hal'] ?? null),
            'dasar_pelaksanaan' => (string) ($payload['dasar_pelaksanaan'] ?? ''),
            'pendahuluan' => (string) ($payload['pendahuluan'] ?? ''),
            'pelaksanaan_1' => (string) ($payload['pelaksanaan_1'] ?? ''),
            'pelaksanaan_2' => (string) ($payload['pelaksanaan_2'] ?? ''),
            'pelaksanaan_3' => (string) ($payload['pelaksanaan_3'] ?? ''),
            'pelaksanaan_4' => (string) ($payload['pelaksanaan_4'] ?? ''),
            'pelaksanaan_5' => (string) ($payload['pelaksanaan_5'] ?? ''),
            'penutup' => (string) ($payload['penutup'] ?? ''),
            'level' => $level,
            'area_id' => $areaId,
            'created_by' => $createdBy,
        ]);

        $rows = $this->attachmentService->buildAttachmentRows($report, $payload, $level, $areaId, $createdBy);
        $this->repository->storeAttachments($report, $rows);

        return $report->fresh(['attachments']) ?? $report->load('attachments');
    }

    private function textOrNull(mixed $value): ?string
    {
        $text = trim((string) ($value ?? ''));

        return $text !== '' ? $text : null;
    }
}
