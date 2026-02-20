<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Catatan Keluarga</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111827; }
        .title { font-size: 16px; font-weight: 700; text-align: center; margin-bottom: 8px; }
        .meta { margin-bottom: 8px; font-size: 11px; }
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

    <div class="title">CATATAN KELUARGA {{ $levelLabel }}</div>
    <div class="meta">
        {{ $areaLabel }}: {{ $areaName }}<br>
        Dicetak oleh: {{ $printedBy?->name ?? '-' }}<br>
        Dicetak pada: {{ $printedAt->format('Y-m-d H:i:s') }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">NO</th>
                <th style="width: 165px;">NAMA KEPALA RUMAH TANGGA</th>
                <th style="width: 70px;">JUMLAH ANGGOTA RUMAH TANGGA</th>
                <th style="width: 55px;">KERJA BAKTI</th>
                <th style="width: 62px;">RUKUN KEMATIAN</th>
                <th style="width: 55px;">KEAGAMAAN</th>
                <th style="width: 50px;">JIMPITAN</th>
                <th style="width: 45px;">ARISAN</th>
                <th style="width: 55px;">LAIN-LAIN</th>
                <th>KETERANGAN</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
                <tr>
                    <td class="center">{{ $item['nomor_urut'] }}</td>
                    <td>{{ $item['nama_kepala_rumah_tangga'] }}</td>
                    <td class="center">{{ $item['jumlah_anggota_rumah_tangga'] }}</td>
                    <td class="center">{{ $item['kerja_bakti'] }}</td>
                    <td class="center">{{ $item['rukun_kematian'] }}</td>
                    <td class="center">{{ $item['kegiatan_keagamaan'] }}</td>
                    <td class="center">{{ $item['jimpitan'] }}</td>
                    <td class="center">{{ $item['arisan'] }}</td>
                    <td class="center">{{ $item['lain_lain'] }}</td>
                    <td>{{ $item['keterangan'] ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="center">Catatan Keluarga belum tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>

