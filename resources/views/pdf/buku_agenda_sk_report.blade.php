<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Agenda SK</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
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
            padding: 5px;
            vertical-align: top;
            word-wrap: break-word;
        }
        th {
            background: #f3f4f6;
            text-align: center;
            font-size: 9px;
            font-weight: 700;
        }
        .number-row th {
            font-size: 8px;
            font-weight: 400;
            background: #ffffff;
        }
        .center {
            text-align: center;
        }
        .footer {
            margin-top: 12px;
            font-size: 9px;
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
        'headerTitle' => 'BUKU AGENDA SK',
        'headerRole' => $levelLabel,
        'headerVillage' => $headerVillage,
        'headerKecamatan' => $pdfKecamatanName ?? null,
        'headerYear' => $budgetYearLabel
    ])

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">NO</th>
                <th style="width: 100px;">NOMOR SK</th>
                <th style="width: 80px;">TANGGAL SK</th>
                <th>KEPADA</th>
                <th>PERIHAL / TENTANG</th>
                <th style="width: 100px;">TEMBUSAN</th>
            </tr>
            <tr class="number-row">
                <th>1</th>
                <th>2</th>
                <th>3</th>
                <th>4</th>
                <th>5</th>
                <th>6</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $index => $item)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $item->nomor_sk }}</td>
                    <td class="center">{{ \Carbon\Carbon::parse($item->tanggal_sk)->format('d/m/Y') }}</td>
                    <td>{{ $item->kepada }}</td>
                    <td>{{ $item->perihal }}</td>
                    <td>{{ $item->tembusan ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="center">Data belum tersedia.</td>
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
