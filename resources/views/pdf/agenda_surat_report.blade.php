<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Agenda Surat Masuk/Keluar</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 8.5px; color: #111827; }
        .lampiran { text-align: right; font-size: 14px; font-weight: 700; margin-bottom: 18px; }
        .title { font-size: 16px; font-weight: 700; text-align: center; margin-bottom: 8px; }
        .meta { margin-bottom: 8px; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { border: 1px solid #111827; padding: 3px; vertical-align: top; word-break: break-word; }
        th { text-align: center; font-size: 8px; font-weight: 700; }
        .number-row th { font-size: 7px; font-weight: 400; }
        .center { text-align: center; }
        .footer-meta { margin-top: 8px; font-size: 9px; color: #374151; }
    </style>
</head>
<body>
    @php
        $scopeLevel = \App\Domains\Wilayah\Enums\ScopeLevel::tryFrom((string) $level);
        $levelLabel = $scopeLevel?->reportLevelLabel() ?? strtoupper((string) $level);
        $areaLabel = $scopeLevel?->reportAreaLabel() ?? 'Wilayah';
    @endphp

    <div class="lampiran">LAMPIRAN 4.10</div>
    <div class="title">BUKU AGENDA SURAT MASUK/KELUAR</div>
    <div class="meta">
        {{ $areaLabel }}: {{ $areaName }}<br>
        Level: {{ $levelLabel }}
    </div>

    <table>
        <thead>
            <tr>
                <th colspan="8">SURAT MASUK</th>
                <th colspan="7">SURAT KELUAR</th>
            </tr>
            <tr>
                <th rowspan="2" style="width: 3%;">NO</th>
                <th colspan="2" style="width: 10%;">TANGGAL</th>
                <th rowspan="2" style="width: 8%;">NOMOR SURAT</th>
                <th rowspan="2" style="width: 10%;">ASAL SURAT DARI</th>
                <th rowspan="2" style="width: 12%;">PERIHAL</th>
                <th rowspan="2" style="width: 7%;">LAMPIRAN</th>
                <th rowspan="2" style="width: 9%;">DITERUSKAN KEPADA</th>
                <th rowspan="2" style="width: 3%;">NO</th>
                <th rowspan="2" style="width: 9%;">NOMOR DAN KODE SURAT</th>
                <th rowspan="2" style="width: 7%;">TANGGAL SURAT</th>
                <th rowspan="2" style="width: 8%;">KEPADA</th>
                <th rowspan="2" style="width: 12%;">PERIHAL</th>
                <th rowspan="2" style="width: 7%;">LAMPIRAN</th>
                <th rowspan="2" style="width: 8%;">TEMBUSAN</th>
            </tr>
            <tr>
                <th style="width: 5%;">TERIMA SURAT</th>
                <th style="width: 5%;">SURAT</th>
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
                <th>13</th>
                <th>14</th>
                <th>15</th>
            </tr>
        </thead>
        <tbody>
            @php
                $nomorMasuk = 0;
                $nomorKeluar = 0;
            @endphp
            @forelse ($items as $index => $item)
                <tr>
                    @if ($item->jenis_surat === 'masuk')
                        @php
                            $nomorMasuk++;
                            $asalDari = collect([$item->asal_surat, $item->dari])
                                ->filter(fn ($value) => filled($value))
                                ->implode(' / ');
                        @endphp
                        <td class="center">{{ $nomorMasuk }}</td>
                        <td class="center">{{ $item->tanggal_terima ? \Carbon\Carbon::parse($item->tanggal_terima)->format('d/m/Y') : '-' }}</td>
                        <td class="center">{{ \Carbon\Carbon::parse($item->tanggal_surat)->format('d/m/Y') }}</td>
                        <td>{{ $item->nomor_surat }}</td>
                        <td>{{ $asalDari !== '' ? $asalDari : '-' }}</td>
                        <td>{{ $item->perihal }}</td>
                        <td>{{ $item->lampiran ?: '-' }}</td>
                        <td>{{ $item->diteruskan_kepada ?: '-' }}</td>
                        <td class="center">-</td>
                        <td class="center">-</td>
                        <td class="center">-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                    @else
                        @php
                            $nomorKeluar++;
                        @endphp
                        <td class="center">-</td>
                        <td class="center">-</td>
                        <td class="center">-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td class="center">{{ $nomorKeluar }}</td>
                        <td>{{ $item->nomor_surat }}</td>
                        <td class="center">{{ \Carbon\Carbon::parse($item->tanggal_surat)->format('d/m/Y') }}</td>
                        <td>{{ $item->kepada ?: '-' }}</td>
                        <td>{{ $item->perihal }}</td>
                        <td>{{ $item->lampiran ?: '-' }}</td>
                        <td>{{ $item->tembusan ?: '-' }}</td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="15" class="center">Data belum tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer-meta">
        Dicetak oleh: {{ $printedBy?->name ?? '-' }} | Dicetak pada: {{ $printedAt->format('Y-m-d H:i:s') }}
    </div>
</body>
</html>
