<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Kegiatan TP PKK</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111827; }
        .title { font-size: 16px; font-weight: 700; text-align: center; margin-bottom: 8px; }
        .meta { margin-bottom: 8px; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { border: 1px solid #111827; padding: 4px; vertical-align: top; word-wrap: break-word; }
        th { background: #f3f4f6; text-align: center; font-size: 9px; }
        .center { text-align: center; }
    </style>
</head>
<body>
    @php
        $scopeLevel = \App\Domains\Wilayah\Enums\ScopeLevel::tryFrom((string) $level);
        $levelLabel = $scopeLevel?->reportLevelLabel() ?? strtoupper((string) $level);
        $areaLabel = $scopeLevel?->reportAreaLabel() ?? 'Wilayah';
    @endphp

    <div class="title">BUKU KEGIATAN TP PKK {{ $levelLabel }}</div>
    <div class="meta">
        {{ $areaLabel }}: {{ $areaName }}<br>
        Dicetak oleh: {{ $printedBy?->name ?? '-' }}<br>
        Dicetak pada: {{ $printedAt->format('Y-m-d H:i:s') }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 24px;">NO</th>
                <th style="width: 96px;">NAMA PETUGAS</th>
                <th style="width: 86px;">JABATAN</th>
                <th style="width: 68px;">TANGGAL</th>
                <th style="width: 92px;">TEMPAT</th>
                <th>URAIAN</th>
                <th style="width: 60px;">STATUS</th>
                <th style="width: 88px;">TANDA TANGAN</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $index => $item)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $item->nama_petugas ?: $item->title ?: '-' }}</td>
                    <td>{{ $item->jabatan_petugas ?: '-' }}</td>
                    <td class="center">{{ $item->activity_date ? \Carbon\Carbon::parse($item->activity_date)->format('d/m/Y') : '-' }}</td>
                    <td>{{ $item->tempat_kegiatan ?: '-' }}</td>
                    <td>{{ $item->uraian ?: ($item->description ?: '-') }}</td>
                    <td class="center">{{ strtoupper((string) ($item->status ?: '-')) }}</td>
                    <td>{{ $item->tanda_tangan ?: ($item->nama_petugas ?: '-') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="center">Data kegiatan belum tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
