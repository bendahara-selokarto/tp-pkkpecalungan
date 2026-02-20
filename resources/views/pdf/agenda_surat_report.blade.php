<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Agenda Surat Masuk/Keluar</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #111827; }
        .title { font-size: 16px; font-weight: 700; text-align: center; margin-bottom: 8px; }
        .meta { margin-bottom: 8px; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { border: 1px solid #111827; padding: 4px; vertical-align: top; word-wrap: break-word; }
        th { background: #f3f4f6; text-align: center; font-size: 8px; }
        .center { text-align: center; }
    </style>
</head>
<body>
    @php
        $scopeLevel = \App\Domains\Wilayah\Enums\ScopeLevel::tryFrom((string) $level);
        $levelLabel = $scopeLevel?->reportLevelLabel() ?? strtoupper((string) $level);
        $areaLabel = $scopeLevel?->reportAreaLabel() ?? 'Wilayah';
    @endphp

    <div class="title">BUKU AGENDA SURAT MASUK/KELUAR {{ $levelLabel }}</div>
    <div class="meta">
        {{ $areaLabel }}: {{ $areaName }}<br>
        Dicetak oleh: {{ $printedBy?->name ?? '-' }}<br>
        Dicetak pada: {{ $printedAt->format('Y-m-d H:i:s') }}
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 22px;">NO</th>
                <th colspan="7">SURAT MASUK</th>
                <th rowspan="2" style="width: 22px;">NO</th>
                <th colspan="6">SURAT KELUAR</th>
            </tr>
            <tr>
                <th style="width: 58px;">TGL TERIMA</th>
                <th style="width: 58px;">TGL SURAT</th>
                <th style="width: 68px;">NOMOR SURAT</th>
                <th style="width: 92px;">ASAL SURAT/DARI</th>
                <th style="width: 112px;">PERIHAL</th>
                <th style="width: 72px;">LAMPIRAN</th>
                <th style="width: 88px;">DITERUSKAN KEPADA</th>
                <th style="width: 82px;">NOMOR DAN KODE SURAT</th>
                <th style="width: 58px;">TGL SURAT</th>
                <th style="width: 90px;">KEPADA</th>
                <th style="width: 112px;">PERIHAL</th>
                <th style="width: 72px;">LAMPIRAN</th>
                <th style="width: 86px;">TEMBUSAN</th>
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
</body>
</html>
