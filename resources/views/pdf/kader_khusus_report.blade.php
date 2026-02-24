<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Kader Khusus</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111827; }
        .title { font-size: 16px; font-weight: 700; text-align: center; margin-bottom: 8px; }
        .meta { margin-bottom: 8px; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { border: 1px solid #111827; padding: 4px; vertical-align: top; word-wrap: break-word; }
        th { background: #f3f4f6; text-align: center; font-size: 10px; }
        .number-row th { font-size: 9px; font-weight: 400; }
        .center { text-align: center; }
    </style>
</head>
<body>
    @php
        $scopeLevel = \App\Domains\Wilayah\Enums\ScopeLevel::tryFrom((string) $level);
        $levelLabel = $scopeLevel?->reportLevelLabel() ?? strtoupper((string) $level);
        $areaLabel = $scopeLevel?->reportAreaLabel() ?? 'Wilayah';
    @endphp

    <div class="title">BUKU KADER KHUSUS {{ $levelLabel }}</div>
    <div class="meta">
        {{ $areaLabel }}: {{ $areaName }}<br>
        Dicetak oleh: {{ $printedBy?->name ?? '-' }}<br>
        Dicetak pada: {{ $printedAt->format('Y-m-d H:i:s') }}
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 28px;">NO</th>
                <th rowspan="2" style="width: 105px;">NAMA</th>
                <th colspan="2" style="width: 80px;">JENIS KELAMIN</th>
                <th rowspan="2" style="width: 125px;">TEMPAT TANGGAL LAHIR</th>
                <th colspan="2" style="width: 80px;">STATUS</th>
                <th rowspan="2" style="width: 120px;">ALAMAT</th>
                <th rowspan="2" style="width: 80px;">PENDIDIKAN</th>
                <th rowspan="2" style="width: 120px;">JENIS KADER KHUSUS</th>
                <th rowspan="2">KETERANGAN</th>
            </tr>
            <tr>
                <th style="width: 40px;">L</th>
                <th style="width: 40px;">P</th>
                <th style="width: 40px;">NIKAH</th>
                <th style="width: 40px;">BLM NIKAH</th>
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
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $index => $item)
                @php
                    $isMale = strtoupper((string) $item->jenis_kelamin) === 'L';
                    $isMarried = (string) $item->status_perkawinan === 'kawin';
                    $ttl = trim((string) $item->tempat_lahir) !== ''
                        ? (string) $item->tempat_lahir
                        : '-';
                    if ($item->tanggal_lahir) {
                        $ttl .= ', ' . \Carbon\Carbon::parse($item->tanggal_lahir)->format('d/m/Y');
                    }
                @endphp
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $item->nama ?: '-' }}</td>
                    <td class="center">{{ $isMale ? 'v' : '-' }}</td>
                    <td class="center">{{ $isMale ? '-' : 'v' }}</td>
                    <td>{{ $ttl }}</td>
                    <td class="center">{{ $isMarried ? 'v' : '-' }}</td>
                    <td class="center">{{ $isMarried ? '-' : 'v' }}</td>
                    <td>{{ $item->alamat ?: '-' }}</td>
                    <td>{{ $item->pendidikan ?: '-' }}</td>
                    <td>{{ $item->jenis_kader_khusus ?: '-' }}</td>
                    <td>{{ $item->keterangan ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="center">Data belum tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
