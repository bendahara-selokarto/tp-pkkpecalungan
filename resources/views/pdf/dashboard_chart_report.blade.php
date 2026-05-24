<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Grafik - Laporan Chart Dashboard</title>
    <style>
        @page { margin: 20px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #000; margin: 0; padding: 0; }
        h1 { margin: 0 0 10px; font-size: 16px; text-transform: uppercase; border-bottom: 2px solid #000; padding-bottom: 5px; }
        h2 { margin: 15px 0 10px; font-size: 12px; text-transform: uppercase; background: #f0f0f0; padding: 4px 8px; border: 1px solid #000; }
        .meta { margin-bottom: 15px; line-height: 1.4; }
        .stats { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .stats th, .stats td { border: 1px solid #000; padding: 5px; text-align: center; }
        .stats th { background: #f0f0f0; font-weight: bold; }

        /* Manual Style Vertical Chart */
        .chart-wrapper { margin-bottom: 40px; page-break-inside: avoid; }
        .chart-area { 
            position: relative; 
            height: 200px; 
            margin-left: 50px; 
            margin-bottom: 70px; /* Space for X labels */
            border-left: 2px solid #000; 
            border-bottom: 2px solid #000; 
            padding-top: 10px;
        }
        
        /* Y Axis Scale */
        .y-scale { position: absolute; left: -45px; top: 0; height: 100%; width: 40px; }
        .y-tick { position: absolute; right: 0; width: 5px; border-top: 1px solid #000; font-size: 8px; text-align: right; padding-right: 8px; }
        
        /* Bar Container */
        .bars-container { 
            height: 200px; 
            width: 100%; 
            white-space: nowrap; 
            overflow: visible;
        }
        
        .label-group { 
            display: inline-block; 
            vertical-align: bottom; 
            text-align: center; 
            position: relative;
            margin: 0 4px;
            width: auto;
        }
        
        .bar-stack { display: inline-block; vertical-align: bottom; }
        .bar { 
            display: inline-block; 
            vertical-align: bottom; 
            background-color: #333; 
            border: 1px solid #000; 
            margin: 0 1px;
            width: 12px;
        }
        .bar-primary { background-color: #000; }
        .bar-secondary { background-color: #555; }
        .bar-tertiary { background-color: #aaa; }
        
        .x-label { 
            position: absolute; 
            top: 5px; 
            left: 50%; 
            width: 120px;
            margin-left: -60px;
            font-size: 8px; 
            transform: rotate(-45deg); 
            transform-origin: top center;
            text-align: right;
            white-space: normal;
            line-height: 1;
        }

        .legend { margin-top: 10px; font-size: 9px; }
        .legend-item { display: inline-block; margin-right: 15px; }
        .legend-box { display: inline-block; width: 10px; height: 10px; border: 1px solid #000; vertical-align: middle; margin-right: 4px; }

        .footer { margin-top: 30px; font-size: 8px; border-top: 1px solid #ccc; padding-top: 5px; }
    </style>
</head>
<body>
    @php
        $activityStats = $stats['activity'] ?? [];
        $documentStats = $stats['documents'] ?? [];
        $activityCharts = $charts['activity'] ?? [];
        $documentCharts = $charts['documents'] ?? [];
        
        $budgetYearLabel = (string) ($filters['tahun_anggaran'] ?? ($printedBy?->active_budget_year ?? '-'));

        // Prepare chart structure for unified rendering
        $allCharts = [];
        
        // Add Data Umum Charts (Primary)
        if (!empty($dataUmumCharts)) {
            foreach($dataUmumCharts as $key => $c) {
                $allCharts[] = $c;
            }
        }

        // Add Activity Charts
        if (!empty($activityCharts['monthly']['labels'])) {
            $allCharts[] = [
                'title' => 'Aktivitas Bulanan',
                'labels' => (array) $activityCharts['monthly']['labels'],
                'series' => ['Kegiatan' => (array) $activityCharts['monthly']['values']]
            ];
        }

        // Add Document Charts
        if (!empty($documentCharts['coverage_per_buku']['labels'])) {
            $allCharts[] = [
                'title' => 'Cakupan Dokumen per Buku',
                'labels' => (array) $documentCharts['coverage_per_buku']['labels'],
                'series' => ['Entri' => (array) $documentCharts['coverage_per_buku']['values']]
            ];
        }
    @endphp

    <h1>Buku Grafik PKK</h1>
    
    <div class="meta">
        <strong>Wilayah:</strong> {{ $printedBy?->area?->name ?? '-' }} ({{ strtoupper((string) ($printedBy?->scope ?? '-')) }})<br>
        <strong>Tahun Anggaran:</strong> {{ $budgetYearLabel }}<br>
        <strong>Dicetak Oleh:</strong> {{ $printedBy?->name ?? '-' }} pada {{ $printedAt->format('d/m/Y H:i') }}
    </div>

    <table class="stats">
        <thead>
            <tr>
                <th>Total Kegiatan</th>
                <th>Kegiatan Bulan Ini</th>
                <th>Total Buku</th>
                <th>Buku Terisi</th>
                <th>Cakupan (%)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalBuku = (int) ($documentStats['total_buku_tracked'] ?? 0);
                $bukuTerisi = (int) ($documentStats['buku_terisi'] ?? 0);
                $percent = $totalBuku > 0 ? round(($bukuTerisi / $totalBuku) * 100, 1) : 0;
            @endphp
            <tr>
                <td>{{ number_format((int) ($activityStats['total'] ?? 0), 0, ',', '.') }}</td>
                <td>{{ number_format((int) ($activityStats['this_month'] ?? 0), 0, ',', '.') }}</td>
                <td>{{ number_format($totalBuku, 0, ',', '.') }}</td>
                <td>{{ number_format($bukuTerisi, 0, ',', '.') }}</td>
                <td>{{ $percent }}%</td>
            </tr>
        </tbody>
    </table>

    @foreach($allCharts as $chart)
        @php
            $allValues = [];
            foreach($chart['series'] as $seriesData) {
                foreach($seriesData as $val) $allValues[] = (int) $val;
            }
            $maxValue = !empty($allValues) ? max($allValues) : 0;
            
            // Fixed height for bars (reliable in PDF)
            $canvasHeight = 200; 

            // Calculate Y scale
            if ($maxValue <= 5) { $limit = 5; $step = 1; }
            elseif ($maxValue <= 10) { $limit = 10; $step = 2; }
            elseif ($maxValue <= 50) { $limit = 50; $step = 10; }
            elseif ($maxValue <= 100) { $limit = 100; $step = 20; }
            else { 
                $limit = ceil($maxValue / 100) * 100; 
                $step = $limit / 5;
            }
            
            $ticks = [];
            for ($i = 0; $i <= $limit; $i += $step) { $ticks[] = $i; }
            $ticks = array_reverse($ticks);

            $barColors = ['bar-primary', 'bar-secondary', 'bar-tertiary'];
        @endphp

        <div class="chart-wrapper">
            <h2>{{ $chart['title'] }}</h2>
            
            <div class="chart-area">
                <div class="y-scale">
                    @foreach($ticks as $tickValue)
                        @php $topPos = (1 - ($tickValue / $limit)) * 100; @endphp
                        <div class="y-tick" style="top: {{ $topPos }}%;">
                            {{ number_format($tickValue, 0, ',', '.') }}
                        </div>
                    @endforeach
                </div>

                <div class="bars-container">
                    @foreach($chart['labels'] as $idx => $label)
                        <div class="label-group">
                            <div class="bar-stack">
                                @php $colorIdx = 0; @endphp
                                @foreach($chart['series'] as $seriesName => $values)
                                    @php 
                                        $val = (int) ($values[$idx] ?? 0);
                                        $barPx = ($val / $limit) * $canvasHeight;
                                        $colorClass = $barColors[$colorIdx % count($barColors)];
                                        $colorIdx++;
                                    @endphp
                                    <div class="bar {{ $colorClass }}" style="height: {{ round($barPx) }}px;"></div>
                                @endforeach
                            </div>
                            <div class="x-label">{{ $label }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="legend">
                @php $colorIdx = 0; @endphp
                @foreach(array_keys($chart['series']) as $seriesName)
                    <div class="legend-item">
                        <span class="legend-box {{ $barColors[$colorIdx % count($barColors)] }}"></span>
                        {{ $seriesName }}
                    </div>
                    @php $colorIdx++; @endphp
                @endforeach
            </div>
        </div>
    @endforeach

    @include('pdf.partials._report_footer')

    <div class="footer">
        Dicetak dari Sistem Informasi PKK | Kode: BG-{{ date('Ymd') }}
    </div>
</body>
</html>
