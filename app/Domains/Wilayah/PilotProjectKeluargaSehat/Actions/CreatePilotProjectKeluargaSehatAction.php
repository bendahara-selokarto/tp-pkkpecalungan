<?php

namespace App\Domains\Wilayah\PilotProjectKeluargaSehat\Actions;

use App\Domains\Wilayah\PilotProjectKeluargaSehat\Models\PilotProjectKeluargaSehatReport;
use App\Domains\Wilayah\PilotProjectKeluargaSehat\Repositories\PilotProjectKeluargaSehatRepositoryInterface;
use App\Domains\Wilayah\PilotProjectKeluargaSehat\Services\PilotProjectKeluargaSehatScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class CreatePilotProjectKeluargaSehatAction
{
    public function __construct(
        private readonly PilotProjectKeluargaSehatRepositoryInterface $repository,
        private readonly PilotProjectKeluargaSehatScopeService $scopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {}

    public function execute(array $payload, string $level): PilotProjectKeluargaSehatReport
    {
        $areaId = $this->scopeService->requireUserAreaId();
        $createdBy = (int) auth()->id();
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();

        $report = $this->repository->storeReport([
            'judul_laporan' => (string) ($payload['judul_laporan'] ?? config('pilot_project_keluarga_sehat.module.label')),
            'dasar_hukum' => $payload['dasar_hukum'] ?? null,
            'pendahuluan' => $payload['pendahuluan'] ?? null,
            'maksud_tujuan' => $payload['maksud_tujuan'] ?? null,
            'pelaksanaan' => $payload['pelaksanaan'] ?? null,
            'dokumentasi' => $payload['dokumentasi'] ?? null,
            'penutup' => $payload['penutup'] ?? null,
            'tahun_awal' => (int) ($payload['tahun_awal'] ?? config('pilot_project_keluarga_sehat.module.default_period.tahun_awal', 2021)),
            'tahun_akhir' => (int) ($payload['tahun_akhir'] ?? config('pilot_project_keluarga_sehat.module.default_period.tahun_akhir', 2024)),
            'level' => $level,
            'area_id' => $areaId,
            'created_by' => $createdBy,
            'tahun_anggaran' => $tahunAnggaran,
        ]);

        $this->repository->replaceValues(
            $report,
            is_array($payload['values'] ?? null) ? $payload['values'] : [],
            $level,
            $areaId,
            $createdBy,
            $tahunAnggaran
        );

        return $report->fresh(['values']) ?? $report->load('values');
    }
}
