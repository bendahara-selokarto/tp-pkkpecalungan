<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Kader Khusus</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111827; }
        .title { font-size: 16px; font-weight: 700; text-align: center; margin-bottom: 8px; }
        .meta { margin-bottom: 8px; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; }
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
        $budgetYearLabel = $tahunAnggaran ?? ($printedBy?->active_budget_year ?? now()->format('Y'));
    @endphp

    <div class="title">BUKU KADER KHUSUS {{ $levelLabel }}</div>
    <div class="meta">
        {{ $areaLabel }}: {{ $areaName }}<br>
        Tahun Anggaran: {{ $budgetYearLabel }}<br>
        <br>
        Dicetak pada: {{ $printedAt->format('Y-m-d H:i:s') }}
    </div>

    <table>
        <colgroup>
            <col style="width: 25px;">
            <col style="width: 100px;">
            <col style="width: 35px;">
            <col style="width: 35px;">
            <col style="width: 120px;">
            <col style="width: 45px;">
            <col style="width: 45px;">
            <col style="width: 120px;">
            <col style="width: 80px;">
            <col style="width: 120px;">
            <col style="width: 80px;">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="2">NO</th>
                <th rowspan="2">NAMA</th>
                <th colspan="2">JENIS KELAMIN</th>
                <th rowspan="2">TEMPAT TANGGAL LAHIR</th>
                <th colspan="2">STATUS</th>
                <th rowspan="2">ALAMAT</th>
                <th rowspan="2">PENDIDIKAN</th>
                <th rowspan="2">JENIS KADER KHUSUS</th>
                <th rowspan="2">KETERANGAN</th>
            </tr>
            <tr>
                <th>L</th>
                <th>P</th>
                <th>NIKAH</th>
                <th>BLM NIKAH</th>
            </tr>
            <tr class="number-row">
                @for ($i = 1; $i <= 11; $i++)
                    <th>{{ $i }}</th>
                @endfor
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

    @include('pdf.partials._report_footer')

    
    @include('pdf.partials._report_metadata')
</body>
</html>
