<?php

namespace App\Domains\Wilayah\LaporanTahunanPkk\Repositories;

use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\AgendaSurat\Models\AgendaSurat;
use App\Domains\Wilayah\LaporanTahunanPkk\Models\LaporanTahunanPkkEntry;
use App\Domains\Wilayah\LaporanTahunanPkk\Models\LaporanTahunanPkkReport;
use DateTimeInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LaporanTahunanPkkRepository implements LaporanTahunanPkkRepositoryInterface
{
    public function storeReport(array $payload): LaporanTahunanPkkReport
    {
        return LaporanTahunanPkkReport::query()->create($payload);
    }

    public function updateReport(
        LaporanTahunanPkkReport $report,
        array $payload
    ): LaporanTahunanPkkReport {
        $report->update($payload);

        return $report;
    }

    public function paginateByLevelAndArea(string $level, int $areaId, int $perPage, ?int $creatorIdFilter = null): LengthAwarePaginator
    {
        return LaporanTahunanPkkReport::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->when(is_int($creatorIdFilter), static fn ($query) => $query->where('created_by', $creatorIdFilter))
            ->withCount('entries')
            ->latest('tahun_laporan')
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getByLevelAndArea(string $level, int $areaId, ?int $creatorIdFilter = null): Collection
    {
        return LaporanTahunanPkkReport::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->when(is_int($creatorIdFilter), static fn ($query) => $query->where('created_by', $creatorIdFilter))
            ->withCount('entries')
            ->latest('tahun_laporan')
            ->latest('id')
            ->get();
    }

    public function findReport(int $id): LaporanTahunanPkkReport
    {
        return LaporanTahunanPkkReport::query()
            ->with(['entries' => fn ($query) => $query->orderBy('activity_date')->orderBy('id')])
            ->findOrFail($id);
    }

    public function replaceManualEntries(
        LaporanTahunanPkkReport $report,
        array $entries,
        string $level,
        int $areaId,
        int $createdBy
    ): void {
        DB::transaction(function () use ($report, $entries, $level, $areaId, $createdBy): void {
            $report->entries()->delete();

            if ($entries === []) {
                return;
            }

            $rows = collect($entries)
                ->filter(static fn ($item): bool => is_array($item))
                ->map(static function (array $item) use ($report, $level, $areaId, $createdBy): array {
                    return [
                        'report_id' => $report->id,
                        'bidang' => (string) ($item['bidang'] ?? 'sekretariat'),
                        'activity_date' => $item['activity_date'] ?? null,
                        'description' => (string) ($item['description'] ?? ''),
                        'entry_source' => 'manual',
                        'level' => $level,
                        'area_id' => $areaId,
                        'created_by' => $createdBy,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                })
                ->filter(static fn (array $item): bool => trim($item['description']) !== '')
                ->values()
                ->all();

            if ($rows === []) {
                return;
            }

            LaporanTahunanPkkEntry::query()->insert($rows);
        });
    }

    public function getAutoEntriesByLevelAreaAndYear(
        string $level,
        int $areaId,
        int $year
    ): Collection {
        $activityItems = Activity::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->whereYear('activity_date', $year)
            ->get()
            ->map(fn (Activity $item): array => [
                'bidang' => $this->inferBidang(
                    implode(' ', array_filter([
                        $item->title,
                        $item->uraian,
                        $item->description,
                    ]))
                ),
                'activity_date' => $this->normalizeDateValue($item->activity_date),
                'description' => trim((string) ($item->uraian ?: $item->description ?: $item->title)),
                'source_table' => 'activities',
                'source_id' => (int) $item->id,
            ]);

        $agendaItems = AgendaSurat::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->whereYear('tanggal_surat', $year)
            ->get()
            ->map(fn (AgendaSurat $item): array => [
                'bidang' => $this->inferBidang(
                    implode(' ', array_filter([
                        $item->perihal,
                        $item->keterangan,
                    ]))
                ),
                'activity_date' => $this->normalizeDateValue($item->tanggal_surat),
                'description' => trim(sprintf(
                    'Agenda Surat %s: %s%s',
                    strtoupper((string) $item->jenis_surat),
                    (string) $item->perihal,
                    $item->keterangan ? ' - '.trim((string) $item->keterangan) : ''
                )),
                'source_table' => 'agenda_surats',
                'source_id' => (int) $item->id,
            ]);

        return $activityItems
            ->concat($agendaItems)
            ->filter(static fn (array $item): bool => trim((string) ($item['description'] ?? '')) !== '')
            ->sortBy([
                ['activity_date', 'asc'],
                ['source_id', 'asc'],
            ])
            ->values();
    }

    public function deleteReport(LaporanTahunanPkkReport $report): void
    {
        $report->delete();
    }

    private function inferBidang(string $raw): string
    {
        $text = strtolower(trim($raw));
        if ($text === '') {
            return 'sekretariat';
        }

        if (str_contains($text, 'pokja iv') || str_contains($text, 'pokja 4') || str_contains($text, 'pokja-iv')) {
            return 'pokja-iv';
        }

        if (str_contains($text, 'pokja iii') || str_contains($text, 'pokja 3') || str_contains($text, 'pokja-iii')) {
            return 'pokja-iii';
        }

        if (str_contains($text, 'pokja ii') || str_contains($text, 'pokja 2') || str_contains($text, 'pokja-ii')) {
            return 'pokja-ii';
        }

        if (str_contains($text, 'pokja i') || str_contains($text, 'pokja 1') || str_contains($text, 'pokja-i')) {
            return 'pokja-i';
        }

        return 'sekretariat';
    }

    private function normalizeDateValue(mixed $value): ?string
    {
        if ($value instanceof DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        if (! is_string($value)) {
            return null;
        }

        $raw = trim($value);
        if ($raw === '') {
            return null;
        }

        try {
            return Carbon::parse($raw)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }
}




