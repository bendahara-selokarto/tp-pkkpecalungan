<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Daftar Anggota TP PKK dan Kader</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111827; }
        .title { font-size: 16px; font-weight: 700; text-align: center; margin-bottom: 8px; }
        .meta { margin-bottom: 8px; font-size: 11px; }
        .section-title { font-size: 12px; font-weight: 700; margin: 10px 0 6px; }
        table { width: 100%; border-collapse: collapse; }
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
        $budgetYearLabel = $tahunAnggaran ?? ($printedBy?->active_budget_year ?? now()->format('Y'));
    @endphp

    <div class="title">BUKU DAFTAR ANGGOTA TP PKK DAN KADER {{ $levelLabel }}</div>
    <div class="meta">
        {{ $areaLabel }}: {{ $areaName }}<br>
        Tahun Anggaran: {{ $budgetYearLabel }}<br>
        Dicetak oleh: {{ $printedBy?->name ?? '-' }}<br>
        Dicetak pada: {{ $printedAt->format('Y-m-d H:i:s') }}
    </div>

    <div class="section-title">A. ANGGOTA TIM PENGGERAK PKK</div>
    <table>
        <colgroup>
            <col style="width: 30px;">
            <col style="width: 120px;">
            <col style="width: 100px;">
            <col style="width: 45px;">
            <col style="width: 90px;">
            <col style="width: 100px;">
            <col style="width: 70px;">
            <col style="width: 90px;">
            <col style="width: 90px;">
            <col style="width: 120px;">
        </colgroup>
        <thead>
            <tr>
                <th>NO</th>
                <th>NAMA</th>
                <th>JABATAN</th>
                <th>L/P</th>
                <th>TEMPAT LAHIR</th>
                <th>TANGGAL LAHIR</th>
                <th>STATUS</th>
                <th>PENDIDIKAN</th>
                <th>PEKERJAAN</th>
                <th>KETERANGAN</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($anggotaTimPenggeraks as $index => $item)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $item->nama }}</td>
                    <td>{{ $item->jabatan }}</td>
                    <td class="center">{{ $item->jenis_kelamin }}</td>
                    <td>{{ $item->tempat_lahir }}</td>
                    <td class="center">{{ \Carbon\Carbon::parse($item->tanggal_lahir)->format('d/m/Y') }}</td>
                    <td class="center">{{ $item->status_perkawinan === 'kawin' ? 'Kawin' : 'Tidak Kawin' }}</td>
                    <td>{{ $item->pendidikan }}</td>
                    <td>{{ $item->pekerjaan }}</td>
                    <td>{{ $item->keterangan ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="center">Data anggota tim penggerak belum tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">B. KADER TIM PENGGERAK PKK</div>
    <table>
        <colgroup>
            <col style="width: 30px;">
            <col style="width: 120px;">
            <col style="width: 45px;">
            <col style="width: 90px;">
            <col style="width: 100px;">
            <col style="width: 70px;">
            <col style="width: 90px;">
            <col style="width: 120px;">
            <col style="width: 120px;">
        </colgroup>
        <thead>
            <tr>
                <th>NO</th>
                <th>NAMA</th>
                <th>L/P</th>
                <th>TEMPAT LAHIR</th>
                <th>TANGGAL LAHIR</th>
                <th>STATUS</th>
                <th>PENDIDIKAN</th>
                <th>JENIS KADER TP PKK</th>
                <th>KETERANGAN</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($kaderKhusus as $index => $item)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $item->nama }}</td>
                    <td class="center">{{ $item->jenis_kelamin }}</td>
                    <td>{{ $item->tempat_lahir }}</td>
                    <td class="center">{{ \Carbon\Carbon::parse($item->tanggal_lahir)->format('d/m/Y') }}</td>
                    <td class="center">{{ $item->status_perkawinan === 'kawin' ? 'Kawin' : 'Tidak Kawin' }}</td>
                    <td>{{ $item->pendidikan }}</td>
                    <td>{{ $item->jenis_kader_khusus }}</td>
                    <td>{{ $item->keterangan ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="center">Data kader TP PKK belum tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @include('pdf.partials._report_footer')

    <div class="meta" style="margin-top: 8px; font-size: 9px; color: #374151;">
        Dicetak oleh: {{ $printedBy?->name ?? '-' }} | Dicetak pada: {{ $printedAt->format('Y-m-d H:i:s') }}
    </div>
</body>
</html>

