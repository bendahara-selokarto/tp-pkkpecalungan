<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku HATINYA PKK</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111827; }
        .lampiran { text-align: right; font-size: 14px; font-weight: 700; margin-bottom: 16px; }
        .title { font-size: 16px; font-weight: 700; text-align: center; margin-bottom: 8px; }
        .meta { margin-bottom: 8px; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #111827; padding: 4px; vertical-align: top; word-break: break-word; }
        th { text-align: center; font-size: 10px; font-weight: 700; }
        .number-row th { font-size: 9px; font-weight: 400; }
        .center { text-align: center; }
        .note { margin-top: 8px; font-style: italic; }
        .group-row th { background: #f3e8c8; }
    </style>
</head>
<body>
    @php
        $scopeLevel = \App\Domains\Wilayah\Enums\ScopeLevel::tryFrom((string) $level);
        $levelLabel = $scopeLevel?->reportLevelLabel() ?? strtoupper((string) $level);
        $areaLabel = $scopeLevel?->reportAreaLabel() ?? 'Wilayah';
    @endphp

    <div class="lampiran">LAMPIRAN 4.14.2b</div>
    <div class="title">BUKU HATINYA PKK</div>
    <div class="meta">
        {{ $areaLabel }}: {{ $areaName }} | Level: {{ $levelLabel }} | Tahun Anggaran: {{ $budgetYearLabel ?? '-' }}
    </div>

    <table>
        <colgroup>
            <col style="width: 35px;">
            <col style="width: 180px;">
            <col style="width: 85px;">
            <col style="width: 85px;">
            <col style="width: 80px;">
            <col style="width: 80px;">
            <col style="width: 90px;">
            <col style="width: 90px;">
            <col style="width: 90px;">
            <col style="width: 80px;">
            <col style="width: 90px;">
        </colgroup>
        <thead>
            <tr class="group-row">
                <th rowspan="3">NO</th>
                <th rowspan="3">NAMA WILAYAH</th>
                <th colspan="5">MAKANAN POKOK</th>
                <th colspan="5">PEMANFAATAN PEKARANGAN / HATINYA PKK</th>
            </tr>
            <tr class="group-row">
                <th rowspan="2">BERAS</th>
                <th rowspan="2">NON BERAS</th>
                <th colspan="3">PETERNAKAN</th>
                <th rowspan="2">PERIKANAN</th>
                <th rowspan="2">WARUNG HIDUP</th>
                <th rowspan="2">LUMBUNG HIDUP</th>
                <th rowspan="2">TOGA</th>
                <th rowspan="2">TANAMAN KERAS</th>
            </tr>
            <tr class="group-row">
                <th>UNGGAS</th>
                <th>KAMBING</th>
                <th>SAPI/KERBAU</th>
            </tr>
            <tr class="number-row">
                <th>1</th>
                <th>2</th>
                <th>3</th>
                <th>4</th>
                <th>5</th>
                <th>6</th>
                <th>7</th>
                <th>8</th>
                <th>9</th>
                <th>10</th>
                <th>11</th>
                <th>12</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $index => $item)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $item->kategori_pemanfaatan_lahan }}</td>
                    <td class="center">{{ $item->komoditi }}</td>
                    <td class="center">{{ $item->jumlah_komoditi }}</td>
                    <td class="center">-</td>
                    <td class="center">-</td>
                    <td class="center">-</td>
                    <td class="center">-</td>
                    <td class="center">-</td>
                    <td class="center">-</td>
                    <td class="center">-</td>
                    <td class="center">-</td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="center">Buku HATINYA PKK belum tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="note">
        Kategori : (peternakan, perikanan, warung hidup, toga, tanaman keras, lainnya)
    </div>

    @include('pdf.partials._report_footer')

    
    @include('pdf.partials._report_metadata')
</body>
</html>
