<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Kegiatan PKK Pokja IV</title>
    <style>
        @page { margin: 14px 16px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 7px; color: #111827; margin: 0; }
        .lampiran { text-align: right; font-weight: 700; font-size: 10px; margin-bottom: 4px; }
        .title { text-align: center; font-size: 12px; font-weight: 700; margin-bottom: 4px; }
        .meta { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        .meta td { padding: 1px 4px; }
        .meta .label { width: 80px; font-weight: 700; }
        .meta .sep { width: 8px; text-align: center; font-weight: 700; }
        .pokja { font-size: 10px; font-weight: 700; margin: 4px 0 3px; }
        .main-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .main-table th, .main-table td {
            border: 1px solid #111827;
            padding: 2px 1px;
            text-align: center;
            vertical-align: middle;
            word-break: break-word;
        }
        .main-table th { font-weight: 700; line-height: 1.2; }
        .left { text-align: left; }
        .number-row th { font-size: 6px; }
        .total-row td { font-weight: 700; }
    </style>
</head>
<body>
    @php($totals = is_array($totals ?? null) ? $totals : [])
    <div class="lampiran">LAMPIRAN 4.24</div>
    <div class="title">DATA KEGIATAN PKK</div>

    <table class="meta">
        <tr>
            <td class="label">TP PKK</td>
            <td class="sep">:</td>
            <td>{{ $areaName ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">TAHUN</td>
            <td class="sep">:</td>
            <td>{{ $tahun ?? now()->format('Y') }}</td>
        </tr>
    </table>

    <div class="pokja">POKJA IV</div>

    <table class="main-table">
        <thead>
            <tr>
                <th rowspan="3">NO</th>
                <th rowspan="3">NAMA WILAYAH<br>(DUSUN/DESA/KEL./KEC./KAB./KOTA/PROV)</th>
                <th colspan="5">JUMLAH KADER</th>
                <th rowspan="3">POSYANDU</th>
                <th rowspan="3">IMUNISASI / VAKSINASI BAYI/BALITA</th>
                <th rowspan="3">PKG</th>
                <th rowspan="3">TBC</th>
                <th colspan="7">KELESTARIAN LINGKUNGAN HIDUP</th>
                <th colspan="6">PERENCANAAN SEHAT</th>
                <th colspan="3">PROGRAM UNGGULAN</th>
            </tr>
            <tr>
                <th rowspan="2">KADER KESEHATAN</th>
                <th colspan="4">KADER YANG ADA</th>
                <th colspan="3">JUMLAH RUMAH YANG MEMILIKI</th>
                <th rowspan="2">JUMLAH MCK</th>
                <th colspan="3">JUMLAH KRT YANG MENGGUNAKAN AIR</th>
                <th rowspan="2">JUMLAH PUS</th>
                <th rowspan="2">JUMLAH WUS</th>
                <th colspan="2">JUMLAH AKSEPTOR KB</th>
                <th rowspan="2">JML. KK YANG MEMILIKI TABUNGAN KELUARGA</th>
                <th rowspan="2">JML. KK YANG MEMILIKI ASURANSI KESEHATAN</th>
                <th rowspan="2">KESEHATAN</th>
                <th rowspan="2">KELESTARIAN LINGKUNGAN HIDUP</th>
                <th rowspan="2">PERENCANAAN SEHAT</th>
            </tr>
            <tr>
                <th>GIZI</th>
                <th>KESLING</th>
                <th>PHBS</th>
                <th>KB</th>
                <th>JAMBAN (WC)</th>
                <th>SPAL</th>
                <th>TPS</th>
                <th>PDAM</th>
                <th>SUMUR</th>
                <th>LAIN-LAIN</th>
                <th>L</th>
                <th>P</th>
            </tr>
            <tr class="number-row">
                @for ($column = 1; $column <= 27; $column++)
                    <th>{{ $column }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
                <tr>
                    <td>{{ $item['nomor_urut'] ?? '-' }}</td>
                    <td class="left">{{ $item['nama_wilayah'] ?? '-' }}</td>
                    <td>{{ $item['jumlah_kader_kesehatan'] ?? 0 }}</td>
                    <td>{{ $item['jumlah_kader_gizi'] ?? 0 }}</td>
                    <td>{{ $item['jumlah_kader_kesling'] ?? 0 }}</td>
                    <td>{{ $item['jumlah_kader_phbs'] ?? 0 }}</td>
                    <td>{{ $item['jumlah_kader_kb'] ?? 0 }}</td>
                    <td>{{ $item['jumlah_posyandu'] ?? 0 }}</td>
                    <td>{{ $item['jumlah_imunisasi_vaksinasi_bayi_balita'] ?? 0 }}</td>
                    <td>{{ $item['jumlah_pkg'] ?? 0 }}</td>
                    <td>{{ $item['jumlah_tbc'] ?? 0 }}</td>
                    <td>{{ $item['jumlah_rumah_memiliki_jamban'] ?? 0 }}</td>
                    <td>{{ $item['jumlah_rumah_memiliki_spal'] ?? 0 }}</td>
                    <td>{{ $item['jumlah_rumah_memiliki_tps'] ?? 0 }}</td>
                    <td>{{ $item['jumlah_mck'] ?? 0 }}</td>
                    <td>{{ $item['jumlah_krt_menggunakan_pdam'] ?? 0 }}</td>
                    <td>{{ $item['jumlah_krt_menggunakan_sumur'] ?? 0 }}</td>
                    <td>{{ $item['jumlah_krt_menggunakan_lain_lain'] ?? 0 }}</td>
                    <td>{{ $item['jumlah_pus'] ?? 0 }}</td>
                    <td>{{ $item['jumlah_wus'] ?? 0 }}</td>
                    <td>{{ $item['jumlah_akseptor_kb_l'] ?? 0 }}</td>
                    <td>{{ $item['jumlah_akseptor_kb_p'] ?? 0 }}</td>
                    <td>{{ $item['jumlah_kk_memiliki_tabungan_keluarga'] ?? 0 }}</td>
                    <td>{{ $item['jumlah_kk_memiliki_asuransi_kesehatan'] ?? 0 }}</td>
                    <td>{{ $item['program_unggulan_kesehatan'] ?? 0 }}</td>
                    <td>{{ $item['program_unggulan_kelestarian_lingkungan_hidup'] ?? 0 }}</td>
                    <td>{{ $item['program_unggulan_perencanaan_sehat'] ?? 0 }}</td>
                </tr>
            @empty
                <tr>
                    <td>1</td>
                    <td class="left">-</td>
                    @for ($column = 3; $column <= 27; $column++)
                        <td>0</td>
                    @endfor
                </tr>
            @endforelse
            <tr class="total-row">
                <td></td>
                <td class="left">JUMLAH</td>
                <td>{{ $totals['jumlah_kader_kesehatan'] ?? 0 }}</td>
                <td>{{ $totals['jumlah_kader_gizi'] ?? 0 }}</td>
                <td>{{ $totals['jumlah_kader_kesling'] ?? 0 }}</td>
                <td>{{ $totals['jumlah_kader_phbs'] ?? 0 }}</td>
                <td>{{ $totals['jumlah_kader_kb'] ?? 0 }}</td>
                <td>{{ $totals['jumlah_posyandu'] ?? 0 }}</td>
                <td>{{ $totals['jumlah_imunisasi_vaksinasi_bayi_balita'] ?? 0 }}</td>
                <td>{{ $totals['jumlah_pkg'] ?? 0 }}</td>
                <td>{{ $totals['jumlah_tbc'] ?? 0 }}</td>
                <td>{{ $totals['jumlah_rumah_memiliki_jamban'] ?? 0 }}</td>
                <td>{{ $totals['jumlah_rumah_memiliki_spal'] ?? 0 }}</td>
                <td>{{ $totals['jumlah_rumah_memiliki_tps'] ?? 0 }}</td>
                <td>{{ $totals['jumlah_mck'] ?? 0 }}</td>
                <td>{{ $totals['jumlah_krt_menggunakan_pdam'] ?? 0 }}</td>
                <td>{{ $totals['jumlah_krt_menggunakan_sumur'] ?? 0 }}</td>
                <td>{{ $totals['jumlah_krt_menggunakan_lain_lain'] ?? 0 }}</td>
                <td>{{ $totals['jumlah_pus'] ?? 0 }}</td>
                <td>{{ $totals['jumlah_wus'] ?? 0 }}</td>
                <td>{{ $totals['jumlah_akseptor_kb_l'] ?? 0 }}</td>
                <td>{{ $totals['jumlah_akseptor_kb_p'] ?? 0 }}</td>
                <td>{{ $totals['jumlah_kk_memiliki_tabungan_keluarga'] ?? 0 }}</td>
                <td>{{ $totals['jumlah_kk_memiliki_asuransi_kesehatan'] ?? 0 }}</td>
                <td>{{ $totals['program_unggulan_kesehatan'] ?? 0 }}</td>
                <td>{{ $totals['program_unggulan_kelestarian_lingkungan_hidup'] ?? 0 }}</td>
                <td>{{ $totals['program_unggulan_perencanaan_sehat'] ?? 0 }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
