<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Prestasi</title>
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
    @endphp

    <div class="title">BUKU PRESTASI {{ $levelLabel }}</div>
    <div class="meta">
        {{ $areaLabel }}: {{ $areaName }}<br>
        Tahun anggaran: {{ $budgetYearLabel ?? '-' }}
    </div>

    <table>
        <colgroup>
            <col style="width: 28px;">
            <col style="width: 64px;">
            <col style="width: 110px;">
            <col style="width: 90px;">
            <col style="width: 45px;">
            <col style="width: 45px;">
            <col style="width: 45px;">
            <col style="width: 45px;">
            <col style="width: 150px;">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="2">NO</th>
                <th rowspan="2">TAHUN</th>
                <th rowspan="2">JENIS LOMBA</th>
                <th rowspan="2">LOKASI</th>
                <th colspan="4">PRESTASI/KEBERHASILAN YANG DICAPAI</th>
                <th rowspan="2">KETERANGAN</th>
            </tr>
            <tr>
                <th>KECAMATAN</th>
                <th>KABUPATEN</th>
                <th>PROVINSI</th>
                <th>NASIONAL</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $index => $item)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td class="center">{{ $item->tahun }}</td>
                    <td>{{ $item->jenis_lomba }}</td>
                    <td>{{ $item->lokasi }}</td>
                    <td class="center">{{ $item->prestasi_kecamatan ? 'v' : '-' }}</td>
                    <td class="center">{{ $item->prestasi_kabupaten ? 'v' : '-' }}</td>
                    <td class="center">{{ $item->prestasi_provinsi ? 'v' : '-' }}</td>
                    <td class="center">{{ $item->prestasi_nasional ? 'v' : '-' }}</td>
                    <td>{{ $item->keterangan ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="center">Data belum tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @include('pdf.partials._report_footer')

    
    @include('pdf.partials._report_metadata')
</body>
</html>
