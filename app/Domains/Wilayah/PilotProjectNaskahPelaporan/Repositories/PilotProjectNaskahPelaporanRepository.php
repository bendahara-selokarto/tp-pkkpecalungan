<?php

namespace App\Domains\Wilayah\PilotProjectNaskahPelaporan\Repositories;

use App\Domains\Wilayah\PilotProjectNaskahPelaporan\Models\PilotProjectNaskahPelaporanReport;
use App\Domains\Wilayah\PilotProjectNaskahPelaporan\Models\PilotProjectNaskahPelaporanPelaksanaanItem;
use App\Domains\Wilayah\PilotProjectNaskahPelaporan\Models\PilotProjectNaskahPelaporanTembusanItem;
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

    public function getByLevelAndArea(string $level, int $areaId, int $tahunAnggaran): Collection
    {
        return PilotProjectNaskahPelaporanReport::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->where('tahun_anggaran', $tahunAnggaran)
            ->with(['pelaksanaanItems', 'tembusanItems'])
            ->withCount('attachments')
            ->latest('id')
            ->get();
    }

    public function paginateByLevelAndArea(string $level, int $areaId, int $tahunAnggaran, int $perPage): LengthAwarePaginator
    {
        return PilotProjectNaskahPelaporanReport::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->where('tahun_anggaran', $tahunAnggaran)
            ->with(['pelaksanaanItems', 'tembusanItems'])
            ->withCount('attachments')
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findReport(int $id): PilotProjectNaskahPelaporanReport
    {
        return PilotProjectNaskahPelaporanReport::query()
            ->with([
                'attachments' => fn ($query) => $query->orderBy('id'),
                'pelaksanaanItems',
                'tembusanItems',
            ])
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

    public function syncPelaksanaanItems(PilotProjectNaskahPelaporanReport $report, array $payload): void
    {
        PilotProjectNaskahPelaporanPelaksanaanItem::query()
            ->where('report_id', $report->id)
            ->delete();

        $now = now();
        $rows = [];

        for ($sequence = 1; $sequence <= 5; $sequence++) {
            $value = trim((string) ($payload["pelaksanaan_{$sequence}"] ?? ''));
            if ($value === '') {
                continue;
            }

            $rows[] = [
                'report_id' => $report->id,
                'sequence' => $sequence,
                'description' => $value,
                'level' => $report->level,
                'area_id' => $report->area_id,
                'created_by' => $report->created_by,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if ($rows !== []) {
            PilotProjectNaskahPelaporanPelaksanaanItem::query()->insert($rows);
        }
    }

    public function syncTembusanItems(PilotProjectNaskahPelaporanReport $report, ?string $value): void
    {
        PilotProjectNaskahPelaporanTembusanItem::query()
            ->where('report_id', $report->id)
            ->delete();

        $values = $this->splitLines($value);
        if ($values === []) {
            return;
        }

        $now = now();
        $rows = [];
        $sequence = 1;

        foreach ($values as $item) {
            $rows[] = [
                'report_id' => $report->id,
                'sequence' => $sequence,
                'value' => $item,
                'level' => $report->level,
                'area_id' => $report->area_id,
                'created_by' => $report->created_by,
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $sequence++;
        }

        PilotProjectNaskahPelaporanTembusanItem::query()->insert($rows);
    }

    public function deleteReport(PilotProjectNaskahPelaporanReport $report): void
    {
        $report->delete();
    }

    /**
     * @return array<int, string>
     */
    private function splitLines(?string $value): array
    {
        $text = trim((string) ($value ?? ''));
        if ($text === '') {
            return [];
        }

        $parts = preg_split('/\\r\\n|\\r|\\n/', $text) ?: [];

        return array_values(array_filter(array_map('trim', $parts), static fn (string $part): bool => $part !== ''));
    }
}
