<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Buku Tamu</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #111827;
        }
        .title {
            font-size: 16px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 10px;
        }
        .meta {
            margin-bottom: 8px;
            font-size: 11px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        th, td {
            border: 1px solid #111827;
            padding: 4px;
            vertical-align: top;
            word-wrap: break-word;
        }
        th {
            background: #f3f4f6;
            text-align: center;
            font-size: 10px;
        }
        .center {
            text-align: center;
        }
        .footer {
            margin-top: 12px;
            font-size: 10px;
            color: #374151;
        }
    </style>
</head>
<body>
    @php
        $scopeLevel = \App\Domains\Wilayah\Enums\ScopeLevel::tryFrom((string) $level);
        $levelLabel = $scopeLevel?->reportLevelLabel() ?? strtoupper((string) $level);
        
        $headerVillage = ($scopeLevel === \App\Domains\Wilayah\Enums\ScopeLevel::DESA) ? $areaName : null;
        $budgetYearLabel = $tahunAnggaran ?? ($printedBy?->active_budget_year ?? now()->format('Y'));
    @endphp

    @include('pdf.partials._report_header', [
        'headerTitle' => 'BUKU TAMU',
        'headerRole' => $levelLabel,
        'headerVillage' => $headerVillage,
        'headerKecamatan' => $pdfKecamatanName ?? null,
        'headerYear' => $budgetYearLabel
    ])

    <table>
        <thead>
            <tr>
                <th style="width: 28px;">NO</th>
                <th style="width: 90px;">TANGGAL</th>
                <th>KETERANGAN</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $index => $item)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td class="center">{{ $item->visit_date ? \Carbon\Carbon::parse((string) $item->visit_date)->format('Y-m-d') : '-' }}</td>
                    <td>{{ $item->description ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="center">Data belum tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @include('pdf.partials._report_footer')

    <div class="footer">
        Total data: {{ $items->count() }}. | 
        Dicetak oleh: {{ $printedBy?->name ?? '-' }} | Dicetak pada: {{ $printedAt->format('Y-m-d H:i:s') }}
    </div>
</body>
</html>
