<?php

namespace App\Domains\Wilayah\PilotProjectKeluargaSehat\Repositories;

use App\Domains\Wilayah\PilotProjectKeluargaSehat\Models\PilotProjectKeluargaSehatReport;
use App\Domains\Wilayah\PilotProjectKeluargaSehat\Models\PilotProjectKeluargaSehatValue;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PilotProjectKeluargaSehatRepository implements PilotProjectKeluargaSehatRepositoryInterface
{
    public function storeReport(array $payload): PilotProjectKeluargaSehatReport
    {
        return PilotProjectKeluargaSehatReport::create($payload);
    }

    public function updateReport(
        PilotProjectKeluargaSehatReport $report,
        array $payload
    ): PilotProjectKeluargaSehatReport {
        $report->update($payload);

        return $report;
    }

    public function getByLevelAndArea(string $level, int $areaId): Collection
    {
        return PilotProjectKeluargaSehatReport::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->withCount('values')
            ->latest('id')
            ->get();
    }

    public function paginateByLevelAndArea(string $level, int $areaId, int $perPage): LengthAwarePaginator
    {
        return PilotProjectKeluargaSehatReport::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->withCount('values')
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findReport(int $id): PilotProjectKeluargaSehatReport
    {
        return PilotProjectKeluargaSehatReport::query()
            ->with(['values' => fn ($query) => $query->orderBy('sort_order')])
            ->findOrFail($id);
    }

    public function findReportByScopeAndPeriod(
        string $level,
        int $areaId,
        int $tahunAwal,
        int $tahunAkhir
    ): ?PilotProjectKeluargaSehatReport {
        return PilotProjectKeluargaSehatReport::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->where('tahun_awal', $tahunAwal)
            ->where('tahun_akhir', $tahunAkhir)
            ->first();
    }

    public function replaceValues(
        PilotProjectKeluargaSehatReport $report,
        array $values,
        string $level,
        int $areaId,
        int $createdBy
    ): void {
        DB::transaction(function () use ($report, $values, $level, $areaId, $createdBy): void {
            $report->values()->delete();

            if ($values === []) {
                return;
            }

            $now = now();
            $rows = collect($values)
                ->map(static function (array $item) use ($report, $level, $areaId, $createdBy, $now): array {
                    return [
                        'report_id' => $report->id,
                        'section' => (string) ($item['section'] ?? 'pilot_project'),
                        'cluster_code' => (string) ($item['cluster_code'] ?? 'SUPPORT'),
                        'indicator_code' => (string) ($item['indicator_code'] ?? ''),
                        'indicator_label' => (string) ($item['indicator_label'] ?? ''),
                        'year' => (int) ($item['year'] ?? 0),
                        'semester' => (int) ($item['semester'] ?? 1),
                        'value' => (int) ($item['value'] ?? 0),
                        'evaluation_note' => $item['evaluation_note'] ?? null,
                        'keterangan_note' => $item['keterangan_note'] ?? null,
                        'sort_order' => (int) ($item['sort_order'] ?? 0),
                        'level' => $level,
                        'area_id' => $areaId,
                        'created_by' => $createdBy,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                })
                ->filter(static fn (array $item): bool => $item['indicator_code'] !== '' && $item['year'] > 0)
                ->values()
                ->all();

            if ($rows === []) {
                return;
            }

            PilotProjectKeluargaSehatValue::query()->insert($rows);
        });
    }

    public function deleteReport(PilotProjectKeluargaSehatReport $report): void
    {
        $report->delete();
    }
}
