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

    <div class="title">BUKU DAFTAR ANGGOTA TP PKK DAN KADER {{ $levelLabel }}</div>
    <div class="meta">
        {{ $areaLabel }}: {{ $areaName }}<br>
        Dicetak oleh: {{ $printedBy?->name ?? '-' }}<br>
        Dicetak pada: {{ $printedAt->format('Y-m-d H:i:s') }}
    </div>

    <div class="section-title">A. ANGGOTA TIM PENGGERAK PKK</div>
    <table>
        <thead>
            <tr>
                <th style="width: 24px;">NO</th>
                <th style="width: 115px;">NAMA</th>
                <th style="width: 96px;">JABATAN</th>
                <th style="width: 42px;">L/P</th>
                <th style="width: 80px;">TEMPAT LAHIR</th>
                <th style="width: 86px;">TANGGAL LAHIR</th>
                <th style="width: 64px;">STATUS</th>
                <th style="width: 80px;">PENDIDIKAN</th>
                <th style="width: 80px;">PEKERJAAN</th>
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

    <div class="section-title">B. KADER KHUSUS</div>
    <table>
        <thead>
            <tr>
                <th style="width: 24px;">NO</th>
                <th style="width: 115px;">NAMA</th>
                <th style="width: 42px;">L/P</th>
                <th style="width: 80px;">TEMPAT LAHIR</th>
                <th style="width: 86px;">TANGGAL LAHIR</th>
                <th style="width: 64px;">STATUS</th>
                <th style="width: 80px;">PENDIDIKAN</th>
                <th style="width: 115px;">JENIS KADER</th>
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
                    <td colspan="9" class="center">Data kader khusus belum tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
