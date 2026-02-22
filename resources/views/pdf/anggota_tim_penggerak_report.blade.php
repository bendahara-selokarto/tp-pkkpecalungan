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
        table.main { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .main th, .main td { border: 1px solid #111827; padding: 4px; vertical-align: top; word-break: break-word; }
        .main th { text-align: center; font-size: 10px; font-weight: 700; }
        .number-row th { font-size: 9px; font-weight: 400; }
        .center { text-align: center; }
        .note { margin-top: 8px; font-size: 10px; }
        .meta { margin-top: 8px; font-size: 9px; color: #374151; }
    </style>
</head>
<body>
    @php
        $scopeLevel = \App\Domains\Wilayah\Enums\ScopeLevel::tryFrom((string) $level);
        $desaKel = $scopeLevel === \App\Domains\Wilayah\Enums\ScopeLevel::DESA ? $areaName : '-';
        $kec = $scopeLevel === \App\Domains\Wilayah\Enums\ScopeLevel::KECAMATAN ? $areaName : '-';
    @endphp

    <div class="lampiran">LAMPIRAN 4.9a</div>
    <div class="title">BUKU DAFTAR ANGGOTA TIM PENGGERAK PKK</div>

    <table class="identity">
        <tr>
            <td class="label">Desa/Kel.</td>
            <td class="dot">:</td>
            <td>{{ $desaKel }}</td>
            <td class="label">Kec.</td>
            <td class="dot">:</td>
            <td>{{ $kec }}</td>
        </tr>
        <tr>
            <td class="label">Kab/Kota</td>
            <td class="dot">:</td>
            <td>-</td>
            <td class="label">Prov.</td>
            <td class="dot">:</td>
            <td>-</td>
        </tr>
    </table>

    <table class="main">
        <thead>
            <tr>
                <th style="width: 3%;">NO</th>
                <th style="width: 8%;">NAMA</th>
                <th style="width: 8%;">JABATAN</th>
                <th style="width: 8%;">JENIS KELAMIN (L/P)</th>
                <th style="width: 8%;">TEMPAT LAHIR</th>
                <th style="width: 12%;">TG/BL/TH.LAHIR/UMUR</th>
                <th style="width: 7%;">STATUS</th>
                <th style="width: 12%;">ALAMAT</th>
                <th style="width: 8%;">PENDIDIKAN</th>
                <th style="width: 8%;">PEKERJAAN</th>
                <th style="width: 18%;">KET</th>
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

    <div class="meta">
        Dicetak oleh: {{ $printedBy?->name ?? '-' }} | Dicetak pada: {{ $printedAt->format('Y-m-d H:i:s') }}
    </div>
</body>
</html>

