<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Catatan Data dan Kegiatan Warga TP PKK Kabupaten/Kota</title>
    <style>
        @page { margin: 14px 16px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 7px; color: #111827; margin: 0; }
        .lampiran { text-align: right; font-weight: 700; font-size: 10px; margin-bottom: 4px; }
        .title { text-align: center; font-size: 12px; font-weight: 700; margin-bottom: 4px; letter-spacing: 0.2px; }
        .subtitle { text-align: center; font-size: 10px; font-weight: 700; margin-bottom: 8px; }
        .meta-wrap { width: 100%; margin-bottom: 6px; border-collapse: collapse; }
        .meta-wrap td { padding: 1px 4px; vertical-align: top; }
        .meta-wrap .label { width: 140px; font-weight: 700; }
        .meta-wrap .sep { width: 8px; text-align: center; font-weight: 700; }
        .main-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .main-table th, .main-table td {
            border: 1px solid #111827;
            padding: 2px 1px;
            vertical-align: middle;
            word-break: break-word;
        }
        .main-table th {
            text-align: center;
            font-weight: 700;
            line-height: 1.2;
        }
        .header-group { font-size: 7px; }
        .header-sub { font-size: 6px; }
        .header-mini { font-size: 6px; }
        .header-number { font-size: 6px; }
        .center { text-align: center; }
        .left { text-align: left; }
        .meta-footer { margin-top: 6px; font-size: 7px; }
    </style>
</head>
<body>
    @php
        $scopeLevel = \App\Domains\Wilayah\Enums\ScopeLevel::tryFrom((string) $level);
        $levelLabel = $scopeLevel?->reportLevelLabel() ?? strtoupper((string) $level);
        $reportYear = $tahun ?? now()->format('Y');
    @endphp

    <div class="lampiran">LAMPIRAN 4.17c</div>
    <div class="title">CATATAN DATA DAN KEGIATAN WARGA</div>
    <div class="subtitle">TP PKK KABUPATEN/KOTA TAHUN {{ $reportYear }}</div>

    <table class="meta-wrap">
        <tr>
            <td class="label">KECAMATAN</td>
            <td class="sep">:</td>
            <td>{{ $kecamatanName ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">KAB/KOTA</td>
            <td class="sep">:</td>
            <td>{{ $kabKotaName ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">PROVINSI</td>
            <td class="sep">:</td>
            <td>{{ $provinsiName ?? '-' }}</td>
        </tr>
    </table>

    <table class="main-table">
        <thead>
            <tr class="header-group">
                <th rowspan="3">NO</th>
                <th rowspan="3">NAMA KECAMATAN</th>
                <th rowspan="3">JML DESA/KEL</th>
                <th rowspan="3">JML DUSUN/LINGK</th>
                <th rowspan="3">JUML RW</th>
                <th rowspan="3">JUML RT</th>
                <th rowspan="3">JUML DASAWISMA</th>
                <th rowspan="3">JUML KRT</th>
                <th rowspan="3">JUML KK</th>
                <th colspan="11">JUMLAH ANGGOTA KELUARGA</th>
                <th colspan="4">KRITERIA RUMAH</th>
                <th colspan="4">SUMBER AIR KELUARGA</th>
                <th rowspan="3">JUMLAH SARANA MCK</th>
                <th colspan="2">MAKANAN POKOK</th>
                <th colspan="4">WARGA MENGIKUTI KEGIATAN</th>
                <th rowspan="3">KETERANGAN</th>
            </tr>
            <tr class="header-sub">
                <th colspan="2">TOTAL</th>
                <th colspan="2">BALITA</th>
                <th rowspan="2">PUS</th>
                <th rowspan="2">WUS</th>
                <th rowspan="2">IBU HAMIL</th>
                <th rowspan="2">IBU MENYUSUI</th>
                <th rowspan="2">LANSIA</th>
                <th colspan="2">3 BUTA</th>
                <th rowspan="2">SEHAT LAYAK HUNI</th>
                <th rowspan="2">TIDAK SEHAT LAYAK HUNI</th>
                <th rowspan="2">MEMILIKI TTMP. PEMB SAMPAH</th>
                <th rowspan="2">MEMILIKI SPAL DAN PENYERAPAN AIR</th>
                <th rowspan="2">PDAM</th>
                <th rowspan="2">SUMUR</th>
                <th rowspan="2">SUNGAI</th>
                <th rowspan="2">DLL</th>
                <th rowspan="2">BERAS</th>
                <th rowspan="2">NON BERAS</th>
                <th rowspan="2">UP2K</th>
                <th rowspan="2">PEMANFAATAN TANAH PEKARANGAN</th>
                <th rowspan="2">INDUSTRI RUMAH TANGGA</th>
                <th rowspan="2">KESEHATAN LINGKUNGAN</th>
            </tr>
            <tr class="header-mini">
                <th>L</th>
                <th>P</th>
                <th>L</th>
                <th>P</th>
                <th>L</th>
                <th>P</th>
            </tr>
            <tr class="header-number">
                @for ($column = 1; $column <= 36; $column++)
                    <th>{{ $column }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
                <tr>
                    <td class="center">{{ $item['nomor_urut'] }}</td>
                    <td class="left">{{ $item['nama_kecamatan'] ?? '-' }}</td>
                    <td class="center">{{ $item['jml_desa_kelurahan'] ?? 0 }}</td>
                    <td class="center">{{ $item['jml_dusun_lingkungan'] ?? 0 }}</td>
                    <td class="center">{{ $item['jml_rw'] ?? 0 }}</td>
                    <td class="center">{{ $item['jml_rt'] ?? 0 }}</td>
                    <td class="center">{{ $item['jml_dasawisma'] ?? 0 }}</td>
                    <td class="center">{{ $item['jml_krt'] ?? 0 }}</td>
                    <td class="center">{{ $item['jml_kk'] ?? 0 }}</td>
                    <td class="center">{{ $item['total_l'] ?? 0 }}</td>
                    <td class="center">{{ $item['total_p'] ?? 0 }}</td>
                    <td class="center">{{ $item['balita_l'] ?? 0 }}</td>
                    <td class="center">{{ $item['balita_p'] ?? 0 }}</td>
                    <td class="center">{{ $item['pus'] ?? 0 }}</td>
                    <td class="center">{{ $item['wus'] ?? 0 }}</td>
                    <td class="center">{{ $item['ibu_hamil'] ?? 0 }}</td>
                    <td class="center">{{ $item['ibu_menyusui'] ?? 0 }}</td>
                    <td class="center">{{ $item['lansia'] ?? 0 }}</td>
                    <td class="center">{{ $item['tiga_buta_l'] ?? 0 }}</td>
                    <td class="center">{{ $item['tiga_buta_p'] ?? 0 }}</td>
                    <td class="center">{{ $item['sehat_layak_huni'] ?? 0 }}</td>
                    <td class="center">{{ $item['tidak_sehat_layak_huni'] ?? 0 }}</td>
                    <td class="center">{{ $item['memiliki_tempat_sampah'] ?? 0 }}</td>
                    <td class="center">{{ $item['memiliki_spal'] ?? 0 }}</td>
                    <td class="center">{{ $item['pdam'] ?? 0 }}</td>
                    <td class="center">{{ $item['sumur'] ?? 0 }}</td>
                    <td class="center">{{ $item['sungai'] ?? 0 }}</td>
                    <td class="center">{{ $item['dll'] ?? 0 }}</td>
                    <td class="center">{{ $item['jumlah_sarana_mck'] ?? 0 }}</td>
                    <td class="center">{{ $item['beras'] ?? 0 }}</td>
                    <td class="center">{{ $item['non_beras'] ?? 0 }}</td>
                    <td class="center">{{ $item['up2k'] ?? 0 }}</td>
                    <td class="center">{{ $item['pemanfaatan_tanah_pekarangan'] ?? 0 }}</td>
                    <td class="center">{{ $item['industri_rumah_tangga'] ?? 0 }}</td>
                    <td class="center">{{ $item['kesehatan_lingkungan'] ?? 0 }}</td>
                    <td class="left">{{ $item['ket'] ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="36" class="center">Data rekap belum tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="meta-footer">
        Level: {{ $levelLabel }} | Dicetak oleh: {{ $printedBy?->name ?? '-' }} | Dicetak pada: {{ $printedAt->format('Y-m-d H:i:s') }}
    </div>
</body>
</html>
