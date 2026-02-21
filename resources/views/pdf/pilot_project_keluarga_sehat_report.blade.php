<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pilot Project Keluarga Sehat</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            color: #111827;
        }
        .title {
            font-size: 12px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 6px;
        }
        .meta {
            margin-bottom: 8px;
            font-size: 9px;
        }
        .section-title {
            margin-top: 8px;
            margin-bottom: 4px;
            font-size: 10px;
            font-weight: 700;
        }
        .narrative-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .narrative-grid td {
            border: 1px solid #111827;
            vertical-align: top;
            padding: 4px;
        }
        .narrative-label {
            width: 120px;
            font-weight: 700;
            background: #f3f4f6;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        table.data-table th,
        table.data-table td {
            border: 1px solid #111827;
            padding: 3px;
            vertical-align: top;
            word-wrap: break-word;
        }
        table.data-table th {
            background: #f3f4f6;
            text-align: center;
            font-size: 8px;
        }
        .center {
            text-align: center;
        }
        .section-break {
            margin-top: 6px;
        }
        .cluster-heading {
            background: #eef2ff;
            font-weight: 700;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    @php
        $reportItems = collect($reports ?? $items ?? []);
        $catalogSections = is_array($sections ?? null) ? $sections : config('pilot_project_keluarga_sehat.sections', []);
        $scopeLevel = \App\Domains\Wilayah\Enums\ScopeLevel::tryFrom((string) $level);
        $levelLabel = $scopeLevel?->reportLevelLabel() ?? strtoupper((string) $level);
        $areaLabel = $scopeLevel?->reportAreaLabel() ?? 'Wilayah';
    @endphp

    @forelse ($reportItems as $reportIndex => $report)
        @php
            $reportValues = collect(data_get($report, 'values', []));
            $valueMap = $reportValues->keyBy(static function ($value): string {
                return implode('|', [
                    (string) data_get($value, 'section'),
                    (string) data_get($value, 'cluster_code'),
                    (string) data_get($value, 'indicator_code'),
                    (int) data_get($value, 'year'),
                    (int) data_get($value, 'semester'),
                ]);
            });
            $tahunAwal = (int) data_get($report, 'tahun_awal', 2021);
            $tahunAkhir = (int) data_get($report, 'tahun_akhir', $tahunAwal);
            if ($tahunAkhir < $tahunAwal) {
                $tahunAkhir = $tahunAwal;
            }
            $periodYears = range($tahunAwal, $tahunAkhir);
        @endphp

        <div class="title">LAPORAN PELAKSANAAN PILOT PROJECT GERAKAN KELUARGA SEHAT TANGGAP DAN TANGGUH BENCANA {{ $levelLabel }}</div>
        <div class="meta">
            {{ $areaLabel }}: {{ $areaName }}<br>
            Judul laporan: {{ data_get($report, 'judul_laporan', '-') }}<br>
            Periode: {{ $tahunAwal }} - {{ $tahunAkhir }}<br>
            Dicetak oleh: {{ $printedBy?->name ?? '-' }}<br>
            Dicetak pada: {{ $printedAt->format('Y-m-d H:i:s') }}
        </div>

        <table class="narrative-grid">
            <tr>
                <td class="narrative-label">Dasar Pelaksanaan</td>
                <td>{{ data_get($report, 'dasar_hukum') ?: '-' }}</td>
            </tr>
            <tr>
                <td class="narrative-label">Pendahuluan</td>
                <td>{{ data_get($report, 'pendahuluan') ?: '-' }}</td>
            </tr>
            <tr>
                <td class="narrative-label">Maksud dan Tujuan</td>
                <td>{{ data_get($report, 'maksud_tujuan') ?: '-' }}</td>
            </tr>
            <tr>
                <td class="narrative-label">Pelaksanaan</td>
                <td>{{ data_get($report, 'pelaksanaan') ?: '-' }}</td>
            </tr>
            <tr>
                <td class="narrative-label">Dokumentasi</td>
                <td>{{ data_get($report, 'dokumentasi') ?: '-' }}</td>
            </tr>
            <tr>
                <td class="narrative-label">Penutup</td>
                <td>{{ data_get($report, 'penutup') ?: '-' }}</td>
            </tr>
        </table>

        @foreach ($catalogSections as $section)
            @php
                $sectionLabel = (string) data_get($section, 'label', '-');
                $storageSection = (string) data_get($section, 'storage_section', 'pilot_project');
                $clusters = collect(data_get($section, 'clusters', []));
                $hasKeteranganColumn = $storageSection === 'data_dukung';
                $totalPeriodColumns = count($periodYears) * 2;
                $totalColumns = 2 + $totalPeriodColumns + 1 + ($hasKeteranganColumn ? 1 : 0);
            @endphp

            <div class="section-title section-break">{{ $sectionLabel }}</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th rowspan="2" style="width: 24px;">NO</th>
                        <th rowspan="2">DATA UTAMA YANG DI MONITOR</th>
                        @foreach ($periodYears as $year)
                            <th colspan="2" style="width: 52px;">{{ $year }}</th>
                        @endforeach
                        <th rowspan="2" style="width: 90px;">EVALUASI</th>
                        @if ($hasKeteranganColumn)
                            <th rowspan="2" style="width: 90px;">KETERANGAN</th>
                        @endif
                    </tr>
                    <tr>
                        @foreach ($periodYears as $year)
                            <th style="width: 26px;">I</th>
                            <th style="width: 26px;">II</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($clusters as $cluster)
                        @php
                            $clusterCode = (string) data_get($cluster, 'code', '-');
                            $clusterLabel = (string) data_get($cluster, 'label', '-');
                            $indicators = collect(data_get($cluster, 'indicators', []));
                            $indicatorNo = 1;
                        @endphp

                        @if ($storageSection === 'pilot_project')
                            <tr class="cluster-heading">
                                <td colspan="{{ $totalColumns }}">{{ $clusterCode }}. {{ $clusterLabel }}</td>
                            </tr>
                        @endif

                        @foreach ($indicators as $indicator)
                            @php
                                $indicatorCode = (string) data_get($indicator, 'code', '');
                                $indicatorLabel = (string) data_get($indicator, 'label', $indicatorCode);
                                $evaluationText = '-';
                                $keteranganText = '-';
                            @endphp
                            <tr>
                                <td class="center">{{ $indicatorNo++ }}</td>
                                <td>{{ $indicatorLabel }}</td>
                                @foreach ($periodYears as $year)
                                    @foreach ([1, 2] as $semester)
                                        @php
                                            $key = implode('|', [$storageSection, $clusterCode, $indicatorCode, $year, $semester]);
                                            $found = $valueMap->get($key);
                                            $nilai = data_get($found, 'value');
                                            $evaluasi = data_get($found, 'evaluation_note');
                                            $keterangan = data_get($found, 'keterangan_note');
                                            if (is_string($evaluasi) && trim($evaluasi) !== '') {
                                                $evaluationText = trim($evaluasi);
                                            }
                                            if (is_string($keterangan) && trim($keterangan) !== '') {
                                                $keteranganText = trim($keterangan);
                                            }
                                        @endphp
                                        <td class="center">{{ is_numeric($nilai) ? (int) $nilai : '-' }}</td>
                                    @endforeach
                                @endforeach
                                <td>{{ $evaluationText }}</td>
                                @if ($hasKeteranganColumn)
                                    <td>{{ $keteranganText }}</td>
                                @endif
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        @endforeach

        @if ($reportIndex !== $reportItems->count() - 1)
            <div class="page-break"></div>
        @endif
    @empty
        <div class="title">LAPORAN PILOT PROJECT KELUARGA SEHAT {{ $levelLabel }}</div>
        <div class="meta">
            {{ $areaLabel }}: {{ $areaName }}<br>
            Dicetak oleh: {{ $printedBy?->name ?? '-' }}<br>
            Dicetak pada: {{ $printedAt->format('Y-m-d H:i:s') }}
        </div>
        <p>Data laporan belum tersedia.</p>
    @endforelse
</body>
</html>
