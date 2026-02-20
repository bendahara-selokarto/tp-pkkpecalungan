<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Pelatihan Kader</title>
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

    <div class="title">DATA PELATIHAN KADER {{ $levelLabel }}</div>
    <div class="meta">
        {{ $areaLabel }}: {{ $areaName }}<br>
        Dicetak oleh: {{ $printedBy?->name ?? '-' }}<br>
        Dicetak pada: {{ $printedAt->format('Y-m-d H:i:s') }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 28px;">NO</th>
                <th style="width: 95px;">NO REGISTRASI</th>
                <th style="width: 110px;">NAMA LENGKAP KADER</th>
                <th style="width: 70px;">TGL/TH MASUK TP PKK</th>
                <th style="width: 90px;">JABATAN/FUNGSI</th>
                <th style="width: 42px;">NO URUT PELATIHAN</th>
                <th style="width: 110px;">JUDUL PELATIHAN</th>
                <th style="width: 90px;">JENIS KRITERIA KADERISASI</th>
                <th style="width: 48px;">TAHUN</th>
                <th style="width: 90px;">INSTITUSI PENYELENGGARA</th>
                <th style="width: 70px;">BERSERTIFIKAT/TIDAK</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $index => $item)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $item->nomor_registrasi }}</td>
                    <td>{{ $item->nama_lengkap_kader }}</td>
                    <td class="center">{{ $item->tanggal_masuk_tp_pkk }}</td>
                    <td>{{ $item->jabatan_fungsi }}</td>
                    <td class="center">{{ $item->nomor_urut_pelatihan }}</td>
                    <td>{{ $item->judul_pelatihan }}</td>
                    <td>{{ $item->jenis_kriteria_kaderisasi }}</td>
                    <td class="center">{{ $item->tahun_penyelenggaraan }}</td>
                    <td>{{ $item->institusi_penyelenggara }}</td>
                    <td class="center">{{ $item->status_sertifikat }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="center">Data Pelatihan Kader belum tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
