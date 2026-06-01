<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data aset (sarana) desa/kelurahan</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111827; }
        .title { font-size: 16px; font-weight: 700; text-align: center; margin-bottom: 8px; }
        .meta { margin-bottom: 8px; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #111827; padding: 4px; vertical-align: top; word-wrap: break-word; }
        th { background: #f3f4f6; text-align: center; font-size: 10px; }
        .center { text-align: center; }
    </style>
</head>
<body>
    @php
        $scopeLevel = \App\Domains\Wilayah\Enums\ScopeLevel::tryFrom((string) $level);
        $levelLabel = $scopeLevel?->reportLevelLabel() ?? strtoupper((string) $level);
        $areaLabel = $scopeLevel?->reportAreaLabel() ?? 'Wilayah';
        $budgetYearLabel = $tahunAnggaran ?? ($printedBy?->active_budget_year ?? now()->format('Y'));
    @endphp

    <div class="title">Data aset (sarana) desa/kelurahan {{ $levelLabel }}</div>
    <div class="meta">
        {{ $areaLabel }}: {{ $areaName }}<br>
        Tahun Anggaran: {{ $budgetYearLabel }}<br>
        <br>
        Dicetak pada: {{ $printedAt->format('Y-m-d H:i:s') }}
    </div>

    <table>
        <colgroup>
            <col style="width: 28px;">
            <col style="width: 170px;">
            <col style="width: 150px;">
            <col style="width: 170px;">
            <col style="width: 130px;">
            <col style="width: 80px;">
        </colgroup>
        <thead>
            <tr>
                <th>NO</th>
                <th>NAMA ASET/SARANA</th>
                <th>NAMA PENGELOLA</th>
                <th>KOMODITI</th>
                <th>KATEGORI</th>
                <th>VOLUME</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $index => $item)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $item->nama_warung_pkk }}</td>
                    <td>{{ $item->nama_pengelola }}</td>
                    <td>{{ $item->komoditi }}</td>
                    <td>{{ $item->kategori }}</td>
                    <td>{{ $item->volume }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="center">Data aset (sarana) desa/kelurahan belum tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @include('pdf.partials._report_footer')

    <div class="meta" style="margin-top: 8px; font-size: 9px; color: #374151;">
        
    </div>
    @include('pdf.partials._report_metadata')
</body>
</html>

