<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Chart Dashboard</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #0f172a; }
        h1 { margin: 0 0 6px; font-size: 18px; }
        h2 { margin: 0 0 8px; font-size: 13px; }
        .muted { color: #475569; }
        .meta { margin-bottom: 10px; }
        .stats { width: 100%; border-collapse: collapse; margin: 8px 0 14px; }
        .stats th, .stats td { border: 1px solid #cbd5e1; padding: 6px; text-align: left; }
        .stats th { background: #f1f5f9; }
        .chart-box { border: 1px solid #cbd5e1; border-radius: 6px; padding: 10px; margin-bottom: 12px; }
        .chart-row { margin-bottom: 6px; }
        .chart-label { display: inline-block; width: 33%; vertical-align: top; padding-right: 6px; box-sizing: border-box; }
        .chart-bar-wrap { display: inline-block; width: 52%; vertical-align: middle; }
        .chart-value { display: inline-block; width: 12%; text-align: right; vertical-align: middle; }
        .bar-track { background: #e2e8f0; border-radius: 3px; height: 12px; width: 100%; }
        .bar-fill { background: #2563eb; border-radius: 3px; height: 12px; }
        .footer { margin-top: 10px; font-size: 9px; color: #334155; }
    </style>
</head>
<body>
    @php
        $activityStats = $stats['activity'] ?? [];
        $documentStats = $stats['documents'] ?? [];
        $activityCharts = $charts['activity'] ?? [];
        $documentCharts = $charts['documents'] ?? [];

        $modeLabel = strtoupper((string) ($filters['mode'] ?? 'all'));
        $levelLabel = strtoupper((string) ($filters['level'] ?? 'all'));
        $subLevelLabel = (string) ($filters['sub_level'] ?? 'all');
        $monthLabel = (string) ($filters['section1_month'] ?? 'all');

        $buildItems = static function (array $labels, array $values): array {
            $items = [];
            $total = max(count($labels), count($values));

            for ($i = 0; $i < $total; $i++) {
                $items[] = [
                    'label' => (string) ($labels[$i] ?? '-'),
                    'value' => (int) ($values[$i] ?? 0),
                ];
            }

            return $items;
        };

        $activityMonthlyItems = $buildItems(
            (array) ($activityCharts['monthly']['labels'] ?? []),
            (array) ($activityCharts['monthly']['values'] ?? [])
        );
        $activityLevelItems = $buildItems(
            (array) ($activityCharts['level']['labels'] ?? []),
            (array) ($activityCharts['level']['values'] ?? [])
        );
        $documentCoverageItems = $buildItems(
            (array) ($documentCharts['coverage_per_buku']['labels'] ?? []),
            (array) ($documentCharts['coverage_per_buku']['values'] ?? [])
        );
        $documentLampiranItems = $buildItems(
            (array) ($documentCharts['coverage_per_lampiran']['labels'] ?? []),
            (array) ($documentCharts['coverage_per_lampiran']['values'] ?? [])
        );
    @endphp

    <h1>Laporan Chart Dashboard</h1>
    <div class="meta muted">
        Area: {{ $printedBy?->area?->name ?? '-' }} | Scope: {{ strtoupper((string) ($printedBy?->scope ?? '-')) }}<br>
        Filter: mode={{ $modeLabel }}, level={{ $levelLabel }}, sub_level={{ $subLevelLabel }}, bulan={{ $monthLabel }}
    </div>

    <table class="stats">
        <thead>
            <tr>
                <th>Total Kegiatan</th>
                <th>Kegiatan Bulan Ini</th>
                <th>Total Buku</th>
                <th>Buku Terisi</th>
                <th>Buku Kosong</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ (int) ($activityStats['total'] ?? 0) }}</td>
                <td>{{ (int) ($activityStats['this_month'] ?? 0) }}</td>
                <td>{{ (int) ($documentStats['total_buku_tracked'] ?? 0) }}</td>
                <td>{{ (int) ($documentStats['buku_terisi'] ?? 0) }}</td>
                <td>{{ (int) ($documentStats['buku_belum_terisi'] ?? 0) }}</td>
            </tr>
        </tbody>
    </table>

    @php
        $chartGroups = [
            ['title' => 'Chart Aktivitas Bulanan', 'items' => $activityMonthlyItems],
            ['title' => 'Chart Distribusi Level Aktivitas', 'items' => $activityLevelItems],
            ['title' => 'Chart Cakupan Dokumen per Buku', 'items' => $documentCoverageItems],
            ['title' => 'Chart Cakupan Dokumen per Lampiran', 'items' => $documentLampiranItems],
        ];
    @endphp

    @foreach ($chartGroups as $group)
        @php
            $values = array_map(static fn (array $item): int => $item['value'], $group['items']);
            $maxValue = max($values ?: [0]);
            $denominator = $maxValue > 0 ? $maxValue : 1;
        @endphp
        <div class="chart-box">
            <h2>{{ $group['title'] }}</h2>

            @forelse ($group['items'] as $item)
                @php
                    $percent = (int) round(($item['value'] / $denominator) * 100);
                @endphp
                <div class="chart-row">
                    <span class="chart-label">{{ $item['label'] }}</span>
                    <span class="chart-bar-wrap">
                        <span class="bar-track">
                            <span class="bar-fill" style="display: block; width: {{ $percent }}%;"></span>
                        </span>
                    </span>
                    <span class="chart-value">{{ $item['value'] }}</span>
                </div>
            @empty
                <div class="muted">Belum ada data chart.</div>
            @endforelse
        </div>
    @endforeach

    <div class="footer">
        Dicetak oleh: {{ $printedBy?->name ?? '-' }} | Dicetak pada: {{ $printedAt->format('Y-m-d H:i:s') }}
    </div>
</body>
</html>
