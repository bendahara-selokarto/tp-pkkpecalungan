<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Anggota Tim Penggerak</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111827; }
        .title { font-size: 16px; font-weight: 700; text-align: center; margin-bottom: 8px; }
        .meta { margin-bottom: 8px; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { border: 1px solid #111827; padding: 4px; vertical-align: top; word-wrap: break-word; }
        th { background: #f3f4f6; text-align: center; font-size: 10px; }
        .center { text-align: center; }
    </style>
</head>
<body>
    <div class="title">Buku Daftar Anggota Tim Penggerak PKK {{ strtoupper($level) }}</div>
    <div class="meta">
        Wilayah: {{ $areaName }}<br>
        Dicetak oleh: {{ $printedBy?->name ?? '-' }}<br>
        Dicetak pada: {{ $printedAt->format('Y-m-d H:i:s') }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 24px;">NO</th>
                <th style="width: 88px;">NAMA</th>
                <th style="width: 76px;">JABATAN</th>
                <th style="width: 50px;">JENIS KELAMIN (L/P)</th>
                <th style="width: 70px;">TEMPAT LAHIR</th>
                <th style="width: 96px;">TG/BL/TH.LAHIR / UMUR</th>
                <th style="width: 62px;">STATUS</th>
                <th style="width: 84px;">ALAMAT</th>
                <th style="width: 62px;">PENDIDIKAN</th>
                <th style="width: 62px;">PEKERJAAN</th>
                <th>KET</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $index => $item)
                @php
                    $umur = \Carbon\Carbon::parse($item->tanggal_lahir)->age;
                @endphp
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $item->nama }}</td>
                    <td>{{ $item->jabatan }}</td>
                    <td class="center">{{ $item->jenis_kelamin }}</td>
                    <td>{{ $item->tempat_lahir }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal_lahir)->format('d/m/Y') }} / {{ $umur }}</td>
                    <td class="center">{{ $item->status_perkawinan === 'kawin' ? 'Kawin' : 'Tidak Kawin' }}</td>
                    <td>{{ $item->alamat }}</td>
                    <td>{{ $item->pendidikan }}</td>
                    <td>{{ $item->pekerjaan }}</td>
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

