<?php

namespace App\Domains\Wilayah\PilotProjectNaskahPelaporan\Actions;

use App\Domains\Wilayah\PilotProjectNaskahPelaporan\Models\PilotProjectNaskahPelaporanReport;
use App\Domains\Wilayah\PilotProjectNaskahPelaporan\Repositories\PilotProjectNaskahPelaporanRepositoryInterface;
use App\Domains\Wilayah\PilotProjectNaskahPelaporan\Services\PilotProjectNaskahPelaporanAttachmentService;

class UpdatePilotProjectNaskahPelaporanAction
{
    public function __construct(
        private readonly PilotProjectNaskahPelaporanRepositoryInterface $repository,
        private readonly PilotProjectNaskahPelaporanAttachmentService $attachmentService
    ) {
    }

    public function execute(PilotProjectNaskahPelaporanReport $report, array $payload): PilotProjectNaskahPelaporanReport
    {
        $updated = $this->repository->updateReport($report, [
            'judul_laporan' => (string) ($payload['judul_laporan'] ?? $report->judul_laporan),
            'surat_kepada' => $this->textOrCurrent($payload, 'surat_kepada', $report->surat_kepada),
            'surat_dari' => $this->textOrCurrent($payload, 'surat_dari', $report->surat_dari),
            'surat_tembusan' => $this->textOrCurrent($payload, 'surat_tembusan', $report->surat_tembusan),
            'surat_tanggal' => $payload['surat_tanggal'] ?? $report->surat_tanggal,
            'surat_nomor' => $this->textOrCurrent($payload, 'surat_nomor', $report->surat_nomor),
            'surat_sifat' => $this->textOrCurrent($payload, 'surat_sifat', $report->surat_sifat),
            'surat_lampiran' => $this->textOrCurrent($payload, 'surat_lampiran', $report->surat_lampiran),
            'surat_hal' => $this->textOrCurrent($payload, 'surat_hal', $report->surat_hal),
            'dasar_pelaksanaan' => (string) ($payload['dasar_pelaksanaan'] ?? $report->dasar_pelaksanaan),
            'pendahuluan' => (string) ($payload['pendahuluan'] ?? $report->pendahuluan),
            'pelaksanaan_1' => (string) ($payload['pelaksanaan_1'] ?? $report->pelaksanaan_1),
            'pelaksanaan_2' => (string) ($payload['pelaksanaan_2'] ?? $report->pelaksanaan_2),
            'pelaksanaan_3' => (string) ($payload['pelaksanaan_3'] ?? $report->pelaksanaan_3),
            'pelaksanaan_4' => (string) ($payload['pelaksanaan_4'] ?? $report->pelaksanaan_4),
            'pelaksanaan_5' => (string) ($payload['pelaksanaan_5'] ?? $report->pelaksanaan_5),
            'penutup' => (string) ($payload['penutup'] ?? $report->penutup),
        ]);

        $removeIds = collect($payload['remove_attachment_ids'] ?? [])
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        if ($removeIds !== []) {
            $toDelete = $this->repository->getAttachmentsByIds($updated, $removeIds);
            $this->attachmentService->deleteFiles($toDelete);
            $this->repository->deleteAttachmentsByIds($updated, $removeIds);
        }

        $rows = $this->attachmentService->buildAttachmentRows(
            $updated,
            $payload,
            (string) $updated->level,
            (int) $updated->area_id,
            (int) auth()->id()
        );

        $this->repository->storeAttachments($updated, $rows);

        return $updated->fresh(['attachments']) ?? $updated->load('attachments');
    }

    private function textOrCurrent(array $payload, string $key, ?string $current): ?string
    {
        if (! array_key_exists($key, $payload)) {
            return $current;
        }

        $text = trim((string) ($payload[$key] ?? ''));

        return $text !== '' ? $text : null;
    }
}
