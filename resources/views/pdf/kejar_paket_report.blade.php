<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Isian Kejar Paket/KF/PAUD</title>
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

    <div class="title">DATA ISIAN KEJAR PAKET/KF/PAUD {{ $levelLabel }}</div>
    <div class="meta">
        {{ $areaLabel }}: {{ $areaName }}<br>
        Tahun Anggaran: {{ $budgetYearLabel }}<br>
        Dicetak oleh: {{ $printedBy?->name ?? '-' }}<br>
        Dicetak pada: {{ $printedAt->format('Y-m-d H:i:s') }}
    </div>

    <table>
        <colgroup>
            <col style="width: 35px;">
            <col style="width: 250px;">
            <col style="width: 150px;">
            <col style="width: 60px;">
            <col style="width: 60px;">
            <col style="width: 60px;">
            <col style="width: 60px;">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="2">NO</th>
                <th rowspan="2">NAMA KEJAR PAKET/KF/PAUD</th>
                <th rowspan="2">JENIS KEJAR PAKET/KF/PAUD</th>
                <th colspan="2">JUMLAH WARGA BELAJAR/SISWA</th>
                <th colspan="2">JUMLAH PENGAJAR</th>
            </tr>
            <tr>
                <th>L</th>
                <th>P</th>
                <th>L</th>
                <th>P</th>
            </tr>
            <tr class="number-row">
                @for ($i = 1; $i <= 7; $i++)
                    <th>{{ $i }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $index => $item)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $item->nama_kejar_paket }}</td>
                    <td>{{ $item->jenis_kejar_paket }}</td>
                    <td class="center">{{ $item->jumlah_warga_belajar_l }}</td>
                    <td class="center">{{ $item->jumlah_warga_belajar_p }}</td>
                    <td class="center">{{ $item->jumlah_pengajar_l }}</td>
                    <td class="center">{{ $item->jumlah_pengajar_p }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="center">Data belum tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @include('pdf.partials._report_footer')

    <div class="meta" style="margin-top: 8px; font-size: 9px; color: #374151;">
        Dicetak oleh: {{ $printedBy?->name ?? '-' }} | Dicetak pada: {{ $printedAt->format('Y-m-d H:i:s') }}
    </div>
</body>
</html>
