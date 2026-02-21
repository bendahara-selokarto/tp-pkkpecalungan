<?php

namespace App\Domains\Wilayah\PilotProjectKeluargaSehat\Repositories;

use App\Domains\Wilayah\PilotProjectKeluargaSehat\Models\PilotProjectKeluargaSehatReport;
use Illuminate\Support\Collection;

interface PilotProjectKeluargaSehatRepositoryInterface
{
    public function storeReport(array $payload): PilotProjectKeluargaSehatReport;

    public function updateReport(
        PilotProjectKeluargaSehatReport $report,
        array $payload
    ): PilotProjectKeluargaSehatReport;

    public function getByLevelAndArea(string $level, int $areaId): Collection;

    public function findReport(int $id): PilotProjectKeluargaSehatReport;

    public function findReportByScopeAndPeriod(
        string $level,
        int $areaId,
        int $tahunAwal,
        int $tahunAkhir
    ): ?PilotProjectKeluargaSehatReport;

    public function replaceValues(
        PilotProjectKeluargaSehatReport $report,
        array $values,
        string $level,
        int $areaId,
        int $createdBy
    ): void;

    public function deleteReport(PilotProjectKeluargaSehatReport $report): void;
}

