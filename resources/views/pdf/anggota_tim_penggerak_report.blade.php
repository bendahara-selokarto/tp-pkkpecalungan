<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Daftar Anggota Tim Penggerak PKK</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111827; }
        .lampiran { text-align: right; font-size: 14px; font-weight: 700; margin-bottom: 18px; }
        .title { text-align: center; font-size: 17px; font-weight: 700; margin-bottom: 16px; }
        .identity { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .identity td { border: none; padding: 1px 4px 1px 0; vertical-align: top; font-size: 11px; }
        .identity .label { width: 78px; font-weight: 700; }
        .identity .dot { width: 10px; text-align: center; font-weight: 700; }
        table.main { width: 100%; border-collapse: collapse; }
        .main th, .main td { border: 1px solid #111827; padding: 4px; vertical-align: top; word-break: break-word; }
        .main th { text-align: center; font-size: 10px; font-weight: 700; }
        .number-row th { font-size: 9px; font-weight: 400; }
        .center { text-align: center; }
        .note { margin-top: 8px; font-size: 10px; }
        .meta { margin-top: 8px; font-size: 9px; color: #374151; }
    </style>
</head>
<body>
    @include('pdf.partials._report_header', [
        'headerTitle' => 'BUKU DAFTAR ANGGOTA TIM PENGGERAK PKK',
        'headerLampiran' => 'LAMPIRAN 4.9a'
    ])

    <table class="main">
        <colgroup>
            <col style="width: 30px;">
            <col style="width: 110px;">
            <col style="width: 90px;">
            <col style="width: 50px;">
            <col style="width: 80px;">
            <col style="width: 100px;">
            <col style="width: 70px;">
            <col style="width: 130px;">
            <col style="width: 80px;">
            <col style="width: 80px;">
            <col style="width: 120px;">
        </colgroup>
        <thead>
            <tr>
                <th>NO</th>
                <th>NAMA</th>
                <th>JABATAN</th>
                <th>JENIS KELAMIN (L/P)</th>
                <th>TEMPAT LAHIR</th>
                <th>TG/BL/TH.LAHIR/UMUR</th>
                <th>STATUS</th>
                <th>ALAMAT</th>
                <th>PENDIDIKAN</th>
                <th>PEKERJAAN</th>
                <th>KET</th>
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
                    $umur = $item->tanggal_lahir ? \Carbon\Carbon::parse($item->tanggal_lahir)->age : null;
                    $status = match ((string) $item->status_perkawinan) {
                        'kawin' => 'Menikah',
                        'cerai_hidup' => 'Cerai Hidup',
                        'cerai_mati' => 'Cerai Mati',
                        'lajang' => 'Lajang',
                        default => 'Tidak Kawin',
                    };
                @endphp
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $item->nama ?: '-' }}</td>
                    <td>{{ $item->jabatan ?: '-' }}</td>
                    <td class="center">{{ $item->jenis_kelamin ?: '-' }}</td>
                    <td>{{ $item->tempat_lahir ?: '-' }}</td>
                    <td class="center">
                        @if ($item->tanggal_lahir)
                            {{ \Carbon\Carbon::parse($item->tanggal_lahir)->format('d/m/Y') }}/{{ $umur ?? '-' }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="center">{{ $status }}</td>
                    <td>{{ $item->alamat ?: '-' }}</td>
                    <td>{{ $item->pendidikan ?: '-' }}</td>
                    <td>{{ $item->pekerjaan ?: '-' }}</td>
                    <td>{{ $item->keterangan ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="center">Data belum tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="note">
        Digunakan untuk di Setiap Jenjang TP PKK.
    </div>

    @include('pdf.partials._report_footer')

    <div class="meta">
        Dicetak oleh: {{ $printedBy?->name ?? '-' }} | Dicetak pada: {{ $printedAt->format('Y-m-d H:i:s') }}
    </div>
</body>
</html>
