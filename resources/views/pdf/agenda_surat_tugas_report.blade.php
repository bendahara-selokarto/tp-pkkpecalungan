<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Agenda Surat Tugas</title>
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
        'headerTitle' => 'AGENDA SURAT TUGAS',
        'headerRole' => $levelLabel,
        'headerVillage' => $headerVillage,
        'headerKecamatan' => $pdfKecamatanName ?? null,
        'headerYear' => $budgetYearLabel
    ])

    <table>
        <colgroup>
            <col style="width: 35px;">
            <col style="width: 140px;">
            <col style="width: 90px;">
            <col style="width: 150px;">
            <col style="width: 200px;">
            <col style="width: 90px;">
            <col style="width: 110px;">
        </colgroup>
        <thead>
            <tr>
                <th>NO</th>
                <th>NOMOR DAN KODE SURAT</th>
                <th>TANGGAL SURAT</th>
                <th>KEPADA</th>
                <th>PERIHAL</th>
                <th>LAMPIRAN</th>
                <th>TEMBUSAN</th>
            </tr>
            <tr class="number-row">
                <th>1</th>
                <th>2</th>
                <th>3</th>
                <th>4</th>
                <th>5</th>
                <th>6</th>
                <th>7</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $index => $item)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $item->nomor_surat }}</td>
                    <td class="center">{{ \Carbon\Carbon::parse($item->tanggal_surat)->format('d/m/Y') }}</td>
                    <td>{{ $item->kepada }}</td>
                    <td>{{ $item->perihal }}</td>
                    <td>{{ $item->lampiran ?: '-' }}</td>
                    <td>{{ $item->tembusan ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="center">Data belum tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @include('pdf.partials._report_footer')

    <div class="footer">
        Total data: {{ $items->count() }}. | 
        
    </div>
    @include('pdf.partials._report_metadata')
</body>
</html>
