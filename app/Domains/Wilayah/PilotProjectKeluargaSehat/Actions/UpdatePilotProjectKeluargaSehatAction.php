<?php

namespace App\Domains\Wilayah\PilotProjectKeluargaSehat\Actions;

use App\Domains\Wilayah\PilotProjectKeluargaSehat\Models\PilotProjectKeluargaSehatReport;
use App\Domains\Wilayah\PilotProjectKeluargaSehat\Repositories\PilotProjectKeluargaSehatRepositoryInterface;

class UpdatePilotProjectKeluargaSehatAction
{
    public function __construct(
        private readonly PilotProjectKeluargaSehatRepositoryInterface $repository
    ) {
    }

    public function execute(PilotProjectKeluargaSehatReport $report, array $payload): PilotProjectKeluargaSehatReport
    {
        $updated = $this->repository->updateReport($report, [
            'judul_laporan' => (string) ($payload['judul_laporan'] ?? $report->judul_laporan),
            'dasar_hukum' => $payload['dasar_hukum'] ?? null,
            'pendahuluan' => $payload['pendahuluan'] ?? null,
            'maksud_tujuan' => $payload['maksud_tujuan'] ?? null,
            'pelaksanaan' => $payload['pelaksanaan'] ?? null,
            'dokumentasi' => $payload['dokumentasi'] ?? null,
            'penutup' => $payload['penutup'] ?? null,
            'tahun_awal' => (int) ($payload['tahun_awal'] ?? $report->tahun_awal),
            'tahun_akhir' => (int) ($payload['tahun_akhir'] ?? $report->tahun_akhir),
        ]);

        if (is_array($payload['values'] ?? null)) {
            $this->repository->replaceValues(
                $updated,
                $payload['values'],
                $updated->level,
                (int) $updated->area_id,
                (int) $updated->created_by
            );
        }

        return $updated->fresh(['values']) ?? $updated->load('values');
    }
}

