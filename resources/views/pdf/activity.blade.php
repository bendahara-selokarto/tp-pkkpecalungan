<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Kegiatan #{{ $activity->id }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            color: #111827;
            line-height: 1.4;
        }
        .lampiran { text-align: right; font-size: 14px; font-weight: 700; margin-bottom: 16px; }
        .title { font-size: 16px; font-weight: 700; text-align: center; margin-bottom: 8px; }
        .meta { margin-bottom: 8px; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { border: 1px solid #111827; padding: 4px; vertical-align: top; word-break: break-word; }
        th { text-align: center; font-size: 10px; font-weight: 700; }
        .number-row th { font-size: 9px; font-weight: 400; }
        .center { text-align: center; }
        .footer { margin-top: 8px; font-size: 9px; color: #374151; }
    </style>
</head>
<body>
    @php
        $scopeLevel = \App\Domains\Wilayah\Enums\ScopeLevel::tryFrom((string) $activity->level);
        $levelLabel = $scopeLevel?->reportLevelLabel() ?? strtoupper((string) $activity->level);
        $areaLabel = $scopeLevel?->reportAreaLabel() ?? 'Wilayah';
    @endphp

    <div class="lampiran">LAMPIRAN 4.13</div>
    <div class="title">BUKU KEGIATAN</div>
    <div class="meta">
        {{ $areaLabel }}: {{ $activity->area?->name ?? '-' }}<br>
        Level: {{ $levelLabel }}
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 4%;">NO</th>
                <th rowspan="2" style="width: 10%;">NAMA</th>
                <th rowspan="2" style="width: 14%;">JABATAN</th>
                <th colspan="3">KEGIATAN</th>
                <th rowspan="2" style="width: 22%;">TANDA TANGAN</th>
            </tr>
            <tr>
                <th style="width: 12%;">TANGGAL</th>
                <th style="width: 12%;">TEMPAT</th>
                <th>URAIAN</th>
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
            <tr>
                <td class="center">1</td>
                <td>{{ $activity->nama_petugas ?: $activity->title }}</td>
                <td>{{ $activity->jabatan_petugas ?: '-' }}</td>
                <td class="center">{{ \Carbon\Carbon::parse($activity->activity_date)->format('d/m/Y') }}</td>
                <td>{{ $activity->tempat_kegiatan ?: ($activity->area?->name ?? '-') }}</td>
                <td>{{ $activity->uraian ?: ($activity->description ?: '-') }}</td>
                <td>{{ $activity->tanda_tangan ?: ($activity->nama_petugas ?: ($activity->creator?->name ?? '-')) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Dicetak oleh: {{ $printedBy?->name ?? '-' }} | Dicetak pada: {{ $printedAt->format('Y-m-d H:i:s') }}
    </div>
</body>
</html>
