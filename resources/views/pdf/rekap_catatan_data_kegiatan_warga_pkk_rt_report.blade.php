<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Catatan Data dan Kegiatan Warga Kelompok PKK RT</title>
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

    <div class="lampiran">LAMPIRAN 4.16b</div>
    <div class="title">REKAPITULASI CATATAN DATA DAN KEGIATAN WARGA KELOMPOK PKK RT</div>

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
                <th rowspan="2">NO</th>
                <th rowspan="2">NAMA DASAWISMA</th>
                <th rowspan="2">JML KRT</th>
                <th rowspan="2">JML KK</th>
                <th colspan="10">JUMLAH ANGGOTA KELUARGA</th>
                <th colspan="6">JUMLAH RUMAH</th>
                <th colspan="3">SUMBER AIR</th>
                <th colspan="2">MAKANAN</th>
                <th colspan="4">WARGA MENGIKUTI KEGIATAN</th>
                <th rowspan="2">KET</th>
            </tr>
            <tr class="header-sub">
                <th colspan="2">TOTAL</th>
                <th colspan="2">BALITA</th>
                <th>PUS</th>
                <th>WUS</th>
                <th>IBU HAMIL</th>
                <th>IBU MENYUSUI</th>
                <th>LANSIA</th>
                <th>3 BUTA</th>
                <th>BERKEBUTUHAN KHUSUS</th>
                <th>SEHAT LAYAK HUNI</th>
                <th>TIDAK SEHAT LAYAK HUNI</th>
                <th>MEMILIKI TTMP/PEMBUANGAN SAMPAH</th>
                <th>MEMILIKI SPAL/PEMBUANGAN AIR</th>
                <th>MEMILIKI SARANA MCK DAN SEPTIC TANK</th>
                <th>PDAM</th>
                <th>SUMUR</th>
                <th>DLL</th>
                <th>BERAS</th>
                <th>NON BERAS</th>
                <th>UP2K</th>
                <th>PEMANFAATAN TANAH PEKARANGAN</th>
                <th>INDUSTRI RUMAH TANGGA</th>
                <th>KESEHATAN LINGKUNGAN</th>
            </tr>
            <tr class="header-number">
                @for ($column = 1; $column <= 30; $column++)
                    <th>{{ $column }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
                <tr>
                    <td class="center">{{ $item['nomor_urut'] }}</td>
                    <td class="left">{{ $item['nama_dasawisma'] }}</td>
                    <td class="center">{{ $item['jml_krt'] }}</td>
                    <td class="center">{{ $item['jml_kk'] }}</td>
                    <td class="center">{{ $item['total_l'] }}</td>
                    <td class="center">{{ $item['total_p'] }}</td>
                    <td class="center">{{ $item['balita_l'] }}</td>
                    <td class="center">{{ $item['balita_p'] }}</td>
                    <td class="center">{{ $item['pus'] }}</td>
                    <td class="center">{{ $item['wus'] }}</td>
                    <td class="center">{{ $item['ibu_hamil'] }}</td>
                    <td class="center">{{ $item['ibu_menyusui'] }}</td>
                    <td class="center">{{ $item['lansia'] }}</td>
                    <td class="center">{{ $item['tiga_buta'] }}</td>
                    <td class="center">{{ $item['berkebutuhan_khusus'] }}</td>
                    <td class="center">{{ $item['sehat_layak_huni'] }}</td>
                    <td class="center">{{ $item['tidak_sehat_layak_huni'] }}</td>
                    <td class="center">{{ $item['memiliki_tempat_sampah'] }}</td>
                    <td class="center">{{ $item['memiliki_spal'] }}</td>
                    <td class="center">{{ $item['memiliki_mck_septic'] }}</td>
                    <td class="center">{{ $item['pdam'] }}</td>
                    <td class="center">{{ $item['sumur'] }}</td>
                    <td class="center">{{ $item['dll'] }}</td>
                    <td class="center">{{ $item['beras'] }}</td>
                    <td class="center">{{ $item['non_beras'] }}</td>
                    <td class="center">{{ $item['up2k'] }}</td>
                    <td class="center">{{ $item['pemanfaatan_tanah_pekarangan'] }}</td>
                    <td class="center">{{ $item['industri_rumah_tangga'] }}</td>
                    <td class="center">{{ $item['kesehatan_lingkungan'] }}</td>
                    <td class="left">{{ $item['ket'] ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="30" class="center">Data rekap belum tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="meta-footer">
        Level: {{ $levelLabel }} | Dicetak oleh: {{ $printedBy?->name ?? '-' }} | Dicetak pada: {{ $printedAt->format('Y-m-d H:i:s') }}
    </div>
</body>
</html>

