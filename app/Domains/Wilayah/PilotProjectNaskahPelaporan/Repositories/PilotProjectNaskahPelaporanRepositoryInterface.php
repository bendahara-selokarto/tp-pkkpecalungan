<?php

namespace App\Domains\Wilayah\PilotProjectNaskahPelaporan\Repositories;

use App\Domains\Wilayah\PilotProjectNaskahPelaporan\Models\PilotProjectNaskahPelaporanReport;
use Illuminate\Support\Collection;

interface PilotProjectNaskahPelaporanRepositoryInterface
{
    public function storeReport(array $payload): PilotProjectNaskahPelaporanReport;

    public function updateReport(
        PilotProjectNaskahPelaporanReport $report,
        array $payload
    ): PilotProjectNaskahPelaporanReport;

    public function getByLevelAndArea(string $level, int $areaId): Collection;

    public function findReport(int $id): PilotProjectNaskahPelaporanReport;

    public function storeAttachments(PilotProjectNaskahPelaporanReport $report, array $rows): void;

    public function getAttachmentsByIds(PilotProjectNaskahPelaporanReport $report, array $ids): Collection;

    public function deleteAttachmentsByIds(PilotProjectNaskahPelaporanReport $report, array $ids): void;

    public function deleteReport(PilotProjectNaskahPelaporanReport $report): void;
}
