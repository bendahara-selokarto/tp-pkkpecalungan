<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Anggota Pokja I</title>
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
    @include('pdf.partials._report_header', [
        'headerTitle' => 'DAFTAR ANGGOTA POKJA I',
        'headerLampiran' => 'LAMPIRAN 4.9',
        'headerKecamatan' => $pdfKecamatanName ?? null,
        'headerYear' => $budgetYearLabel ?? null
    ])

    <table>
        <colgroup>
            <col style="width: 25px;">
            <col style="width: 100px;">
            <col style="width: 90px;">
            <col style="width: 35px;">
            <col style="width: 35px;">
            <col style="width: 110px;">
            <col style="width: 45px;">
            <col style="width: 45px;">
            <col style="width: 120px;">
            <col style="width: 80px;">
            <col style="width: 80px;">
            <col style="width: 100px;">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="2">NO</th>
                <th rowspan="2">NAMA</th>
                <th rowspan="2">JABATAN</th>
                <th colspan="2">JENIS KELAMIN</th>
                <th rowspan="2">TEMP, TGL/BLN/LAHIR (UMUR)</th>
                <th colspan="2">STATUS</th>
                <th rowspan="2">ALAMAT</th>
                <th rowspan="2">PENDIDIKAN</th>
                <th rowspan="2">PEKERJAAN</th>
                <th rowspan="2">KET</th>
            </tr>
            <tr>
                <th>L</th>
                <th>P</th>
                <th>KAWIN</th>
                <th>TIDAK KAWIN</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $index => $item)
                @php
                    $isL = $item->jenis_kelamin === 'L';
                    $isKawin = $item->status_perkawinan === 'kawin';
                    $umur = \Carbon\Carbon::parse($item->tanggal_lahir)->age;
                @endphp
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $item->nama }}</td>
                    <td>{{ $item->jabatan }}</td>
                    <td class="center">{{ $isL ? 'v' : '-' }}</td>
                    <td class="center">{{ $isL ? '-' : 'v' }}</td>
                    <td>{{ $item->tempat_lahir }}, {{ \Carbon\Carbon::parse($item->tanggal_lahir)->format('d/m/Y') }} ({{ $umur }})</td>
                    <td class="center">{{ $isKawin ? 'v' : '-' }}</td>
                    <td class="center">{{ $isKawin ? '-' : 'v' }}</td>
                    <td>{{ $item->alamat }}</td>
                    <td>{{ $item->pendidikan }}</td>
                    <td>{{ $item->pekerjaan }}</td>
                    <td>{{ $item->keterangan ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="center">Data belum tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @include('pdf.partials._report_footer')

    <div style="margin-top: 8px; font-size: 9px; color: #374151;">
        Dicetak oleh: {{ $printedBy?->name ?? '-' }} | Dicetak pada: {{ $printedAt->format('Y-m-d H:i:s') }}
    </div>
</body>
</html>
