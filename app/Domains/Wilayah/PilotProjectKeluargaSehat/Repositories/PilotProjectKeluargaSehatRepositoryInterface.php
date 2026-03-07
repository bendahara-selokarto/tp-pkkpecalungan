<?php

namespace App\Domains\Wilayah\PilotProjectKeluargaSehat\Repositories;

use App\Domains\Wilayah\PilotProjectKeluargaSehat\Models\PilotProjectKeluargaSehatReport;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface PilotProjectKeluargaSehatRepositoryInterface
{
    public function storeReport(array $payload): PilotProjectKeluargaSehatReport;

    public function updateReport(
        PilotProjectKeluargaSehatReport $report,
        array $payload
    ): PilotProjectKeluargaSehatReport;

    public function paginateByLevelAndArea(string $level, int $areaId, int $tahunAnggaran, int $perPage): LengthAwarePaginator;

    public function getByLevelAndArea(string $level, int $areaId, int $tahunAnggaran): Collection;

    public function findReport(int $id): PilotProjectKeluargaSehatReport;

    public function findReportByScopeAndPeriod(
        string $level,
        int $areaId,
        int $tahunAnggaran,
        int $tahunAwal,
        int $tahunAkhir
    ): ?PilotProjectKeluargaSehatReport;

    public function replaceValues(
        PilotProjectKeluargaSehatReport $report,
        array $values,
        string $level,
        int $areaId,
        int $createdBy,
        int $tahunAnggaran
    ): void;

    public function deleteReport(PilotProjectKeluargaSehatReport $report): void;
}
