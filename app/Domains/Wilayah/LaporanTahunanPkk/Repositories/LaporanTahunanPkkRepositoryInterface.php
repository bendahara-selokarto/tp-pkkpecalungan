<?php

namespace App\Domains\Wilayah\LaporanTahunanPkk\Repositories;

use App\Domains\Wilayah\LaporanTahunanPkk\Models\LaporanTahunanPkkReport;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface LaporanTahunanPkkRepositoryInterface
{
    public function storeReport(array $payload): LaporanTahunanPkkReport;

    public function updateReport(
        LaporanTahunanPkkReport $report,
        array $payload
    ): LaporanTahunanPkkReport;

    public function paginateByLevelAndArea(string $level, int $areaId, int $perPage): LengthAwarePaginator;

    public function getByLevelAndArea(string $level, int $areaId): Collection;

    public function findReport(int $id): LaporanTahunanPkkReport;

    public function replaceManualEntries(
        LaporanTahunanPkkReport $report,
        array $entries,
        string $level,
        int $areaId,
        int $createdBy
    ): void;

    public function getAutoEntriesByLevelAreaAndYear(
        string $level,
        int $areaId,
        int $year
    ): Collection;

    public function deleteReport(LaporanTahunanPkkReport $report): void;
}
