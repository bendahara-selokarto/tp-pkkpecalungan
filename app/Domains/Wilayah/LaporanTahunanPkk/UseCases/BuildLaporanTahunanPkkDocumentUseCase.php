<?php

namespace App\Domains\Wilayah\LaporanTahunanPkk\UseCases;

use App\Domains\Wilayah\LaporanTahunanPkk\Models\LaporanTahunanPkkReport;
use App\Domains\Wilayah\LaporanTahunanPkk\Repositories\LaporanTahunanPkkRepositoryInterface;

class BuildLaporanTahunanPkkDocumentUseCase
{
    public function __construct(
        private readonly LaporanTahunanPkkRepositoryInterface $repository
    ) {
    }

    public function execute(LaporanTahunanPkkReport $report): array
    {
        $manualEntries = $report->entries
            ->map(fn ($item): array => [
                'bidang' => $item->bidang,
                'activity_date' => $item->activity_date?->format('Y-m-d'),
                'description' => (string) $item->description,
                'source_table' => 'laporan_tahunan_pkk_entries',
                'source_id' => (int) $item->id,
            ]);

        $autoEntries = $this->repository->getAutoEntriesByLevelAreaAndYear(
            $report->level,
            (int) $report->area_id,
            (int) $report->tahun_laporan
        );

        $entries = $autoEntries
            ->concat($manualEntries)
            ->sortBy([
                ['activity_date', 'asc'],
                ['source_id', 'asc'],
            ])
            ->values();

        $grouped = [];
        foreach (config('laporan_tahunan_pkk.bidang_options', []) as $bidang) {
            $grouped[$bidang] = [];
        }

        foreach ($entries as $entry) {
            $bidang = (string) ($entry['bidang'] ?? 'sekretariat');
            if (! array_key_exists($bidang, $grouped)) {
                $bidang = 'sekretariat';
            }

            $grouped[$bidang][] = [
                'activity_date' => $entry['activity_date'] ?? null,
                'description' => (string) ($entry['description'] ?? ''),
            ];
        }

        return [
            'report' => $report,
            'grouped_entries' => $grouped,
            'bidang_labels' => config('laporan_tahunan_pkk.bidang_labels', []),
        ];
    }
}

