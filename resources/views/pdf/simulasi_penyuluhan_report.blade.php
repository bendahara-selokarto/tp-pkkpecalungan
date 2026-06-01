<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelompok Simulasi dan Penyuluhan</title>
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
        $areaLabel = $scopeLevel?->reportAreaLabel() ?? 'Wilayah';
    @endphp

    <div class="title">KELOMPOK SIMULASI DAN PENYULUHAN</div>
    <div class="meta">
        {{ $areaLabel }}: {{ $areaName }}<br>
        Tahun Anggaran: {{ $budgetYearLabel ?? '-' }}<br>
        <br>
        Dicetak pada: {{ $printedAt->format('Y-m-d H:i:s') }}
    </div>

    <table>
        <colgroup>
            <col style="width: 28px;"> {{-- 1. NO --}}
            <col style="width: 150px;"> {{-- 2. NAMA KEGIATAN --}}
            <col style="width: 150px;"> {{-- 3. JENIS SIMULASI/PENYULUHAN --}}
            <col style="width: 75px;"> {{-- 4. KELOMPOK --}}
            <col style="width: 85px;"> {{-- 5. SOSIALISASI --}}
            <col style="width: 50px;"> {{-- 6. L --}}
            <col style="width: 50px;"> {{-- 7. P --}}
        </colgroup>
        <thead>
            <tr>
                <th rowspan="2">NO</th>
                <th rowspan="2">NAMA KEGIATAN</th>
                <th rowspan="2">JENIS SIMULASI/PENYULUHAN</th>
                <th colspan="2">JUMLAH</th>
                <th colspan="2">JUMLAH KADER</th>
            </tr>
            <tr>
                <th>KELOMPOK</th>
                <th>SOSIALISASI</th>
                <th>L</th>
                <th>P</th>
            </tr>
            <tr>
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
            @forelse ($items as $index => $item)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $item->nama_kegiatan }}</td>
                    <td>{{ $item->jenis_simulasi_penyuluhan }}</td>
                    <td class="center">{{ $item->jumlah_kelompok }}</td>
                    <td class="center">{{ $item->jumlah_sosialisasi }}</td>
                    <td class="center">{{ $item->jumlah_kader_l }}</td>
                    <td class="center">{{ $item->jumlah_kader_p }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="center">Data kelompok simulasi dan penyuluhan belum tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @include('pdf.partials._report_footer')

    <div class="meta" style="margin-top: 8px; font-size: 9px; color: #374151;">
        
    </div>
    @include('pdf.partials._report_metadata')
</body>
</html>

