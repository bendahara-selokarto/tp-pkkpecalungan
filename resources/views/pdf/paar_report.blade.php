<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>BUKU PAAR</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111827; }
        .title { font-size: 16px; font-weight: 700; text-align: center; margin-bottom: 8px; }
        .meta { margin-bottom: 8px; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { border: 1px solid #111827; padding: 4px; vertical-align: top; word-wrap: break-word; }
        th { background: #f3f4f6; text-align: center; font-size: 10px; }
        .center { text-align: center; }
        .signature { margin-top: 20px; border-collapse: collapse; width: 100%; table-layout: fixed; }
        .signature td { border: none; padding: 0; vertical-align: top; font-size: 12px; }
        .signature .placeholder { margin-top: 52px; }
    </style>
</head>
<body>
    <div class="title">BUKU PAAR</div>
    <div class="meta">
        DESA : {{ $level === 'desa' ? $areaName : '-' }}<br>
        KEC : {{ $level === 'kecamatan' ? $areaName : '-' }}
    </div>

    @php
        $rowsByKey = collect($items)
            ->keyBy(fn ($item) => (string) $item->indikator);
    @endphp

    <table>
        <thead>
            <tr>
                <th style="width: 28px;">NO</th>
                <th>INDIKATOR</th>
                <th style="width: 90px;">JUMLAH</th>
                <th style="width: 180px;">KETERANGAN</th>
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

    <table class="signature">
        <tr>
            <td style="width: 100%; text-align: left;">
                Ketua TP. PKK Desa/Kel ............
                <div class="placeholder">( ................................ )</div>
            </td>
        </tr>
    </table>
</body>
</html>
