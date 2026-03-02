<?php

namespace App\Domains\Wilayah\PilotProjectNaskahPelaporan\Repositories;

use App\Domains\Wilayah\PilotProjectNaskahPelaporan\Models\PilotProjectNaskahPelaporanAttachment;
use App\Domains\Wilayah\PilotProjectNaskahPelaporan\Models\PilotProjectNaskahPelaporanReport;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class PilotProjectNaskahPelaporanRepository implements PilotProjectNaskahPelaporanRepositoryInterface
{
    public function storeReport(array $payload): PilotProjectNaskahPelaporanReport
    {
        return PilotProjectNaskahPelaporanReport::query()->create($payload);
    }

    public function updateReport(
        PilotProjectNaskahPelaporanReport $report,
        array $payload
    ): PilotProjectNaskahPelaporanReport {
        $report->update($payload);

        return $report;
    }

    public function getByLevelAndArea(string $level, int $areaId): Collection
    {
        return PilotProjectNaskahPelaporanReport::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->withCount('attachments')
            ->latest('id')
            ->get();
    }

    public function paginateByLevelAndArea(string $level, int $areaId, int $perPage): LengthAwarePaginator
    {
        return PilotProjectNaskahPelaporanReport::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->withCount('attachments')
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findReport(int $id): PilotProjectNaskahPelaporanReport
    {
        return PilotProjectNaskahPelaporanReport::query()
            ->with(['attachments' => fn ($query) => $query->orderBy('id')])
            ->findOrFail($id);
    }

    public function storeAttachments(PilotProjectNaskahPelaporanReport $report, array $rows): void
    {
        if ($rows === []) {
            return;
        }

        $report->attachments()->createMany($rows);
    }

    public function getAttachmentsByIds(PilotProjectNaskahPelaporanReport $report, array $ids): Collection
    {
        if ($ids === []) {
            return collect();
        }

        return $report->attachments()
            ->whereIn('id', $ids)
            ->get();
    }

    public function deleteAttachmentsByIds(PilotProjectNaskahPelaporanReport $report, array $ids): void
    {
        if ($ids === []) {
            return;
        }

        $report->attachments()
            ->whereIn('id', $ids)
            ->delete();
    }

    public function deleteReport(PilotProjectNaskahPelaporanReport $report): void
    {
        $report->delete();
    }
}
