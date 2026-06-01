<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>BUKU PAAR</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111827; }
        .title { font-size: 16px; font-weight: 700; text-align: center; margin-bottom: 8px; }
        .meta { margin-bottom: 8px; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #111827; padding: 4px; vertical-align: top; word-wrap: break-word; }
        th { background: #f3f4f6; text-align: center; font-size: 10px; }
        .center { text-align: center; }
        .footer-meta { margin-top: 12px; font-size: 9px; color: #374151; }
    </style>
</head>
<body>
    <div class="title">BUKU PAAR</div>
    <div class="meta">
        DESA : {{ $level === 'desa' ? $areaName : '-' }}<br>
        KEC : {{ $pdfKecamatanName ?? '-' }}<br>
        Tahun Anggaran: {{ $budgetYearLabel ?? '-' }}
    </div>

    @php
        $rowsByKey = collect($items)
            ->keyBy(fn ($item) => (string) $item->indikator);
    @endphp

    <table>
        <colgroup>
            <col style="width: 35px;">
            <col style="width: 350px;">
            <col style="width: 100px;">
            <col style="width: 200px;">
        </colgroup>
        <thead>
            <tr>
                <th>NO</th>
                <th>INDIKATOR</th>
                <th>JUMLAH</th>
                <th>KETERANGAN</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($indicatorLabels as $key => $label)
                @php
                    $item = $rowsByKey->get($key);
                @endphp
                <tr>
                    <td class="center">{{ $loop->iteration }}</td>
                    <td>{{ $label }}</td>
                    <td class="center">{{ (int) ($item->jumlah ?? 0) }}</td>
                    <td>{{ $item->keterangan ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @include('pdf.partials._report_footer')

    <div class="footer-meta">
        Dicetak oleh: {{ $printedBy?->name ?? '-' }} | Dicetak pada: {{ $printedAt->format('Y-m-d H:i:s') }}
    </div>
</body>
</html>
