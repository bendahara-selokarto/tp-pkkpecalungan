<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Catatan Data dan Kegiatan Warga Kelompok PKK RW</title>
    <style>
        @page { margin: 14px 16px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 7px; color: #111827; margin: 0; }
        .lampiran { text-align: right; font-weight: 700; font-size: 10px; margin-bottom: 4px; }
        .title { text-align: center; font-size: 12px; font-weight: 700; margin-bottom: 8px; letter-spacing: 0.2px; }
        .meta-wrap { width: 100%; margin-bottom: 6px; }
        .meta-wrap td { vertical-align: top; }
        .meta-right { width: 38%; }
        .meta-right table { width: 100%; border-collapse: collapse; }
        .meta-right td { padding: 1px 0; }
        .meta-right .label { width: 100px; font-weight: 700; }
        .meta-right .sep { width: 8px; text-align: center; font-weight: 700; }
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

    <div class="lampiran">LAMPIRAN 4.16c</div>
    <div class="title">CATATAN DATA DAN KEGIATAN WARGA KELOMPOK PKK RW</div>

    <table class="meta-wrap">
        <tr>
            <td></td>
            <td class="meta-right">
                <table>
                    <tr>
                        <td class="label">DASA WISMA</td>
                        <td class="sep">:</td>
                        <td>-</td>
                    </tr>
                    <tr>
                        <td class="label">RT / RW</td>
                        <td class="sep">:</td>
                        <td>-</td>
                    </tr>
                    <tr>
                        <td class="label">DESA/KELURAHAN</td>
                        <td class="sep">:</td>
                        <td>{{ $areaName }}</td>
                    </tr>
                    <tr>
                        <td class="label">TAHUN</td>
                        <td class="sep">:</td>
                        <td>{{ $reportYear }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="main-table">
        <thead>
            <tr class="header-group">
                <th rowspan="3">NO</th>
                <th rowspan="3">NOMOR RT</th>
                <th rowspan="3">JML DASAWISMA</th>
                <th rowspan="3">JML KRT</th>
                <th rowspan="3">JML KK</th>
                <th colspan="11">JUMLAH ANGGOTA KELUARGA</th>
                <th colspan="4">KRITERIA RUMAH</th>
                <th colspan="4">SUMBER AIR KELUARGA</th>
                <th rowspan="3">JUMLAH SARANA MCK</th>
                <th colspan="2">MAKANAN</th>
                <th colspan="4">WARGA MENGIKUTI KEGIATAN</th>
                <th rowspan="3">KET</th>
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
                <th rowspan="2">MEMILIKI TTMP. PEMB. SAMPAH</th>
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
                @for ($column = 1; $column <= 32; $column++)
                    <th>{{ $column }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
                <tr>
                    <td class="center">{{ $item['nomor_urut'] }}</td>
                    <td class="center">{{ $item['nomor_rt'] ?? '-' }}</td>
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
                    <td colspan="32" class="center">Data rekap belum tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="meta-footer">
        Level: {{ $levelLabel }} | Dicetak oleh: {{ $printedBy?->name ?? '-' }} | Dicetak pada: {{ $printedAt->format('Y-m-d H:i:s') }}
    </div>
</body>
</html>
