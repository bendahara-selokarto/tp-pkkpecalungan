<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Kegiatan PKK Pokja III</title>
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
    <div class="lampiran">LAMPIRAN 4.23</div>
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

    <div class="pokja">POKJA III</div>

    <table class="main-table">
        <thead>
            <tr>
                <th rowspan="3">NO</th>
                <th rowspan="3">NAMA WILAYAH<br>(DUSUN/DESA/KEL./KEC/KAB/KOTA/PROV)</th>
                <th colspan="3">JUMLAH KADER</th>
                <th colspan="9">PANGAN</th>
                <th colspan="3">JUMLAH INDUSTRI</th>
                <th colspan="2">JUMLAH</th>
                <th rowspan="3">KETERANGAN</th>
            </tr>
            <tr>
                <th rowspan="2">PANGAN</th>
                <th rowspan="2">SANDANG</th>
                <th rowspan="2">TATA LAKSANA<br>RUMAH TANGGA</th>
                <th colspan="2">MAKANAN POKOK</th>
                <th colspan="7">PEMANFAATAN PEKARANGAN/HATINYA PKK</th>
                <th rowspan="2">PANGAN</th>
                <th rowspan="2">SANDANG</th>
                <th rowspan="2">JASA</th>
                <th rowspan="2">SEHAT LAYAK HUNI</th>
                <th rowspan="2">TIDAK SEHAT DAN TIDAK LAYAK HUNI</th>
            </tr>
            <tr>
                <th>BERAS</th>
                <th>NON BERAS</th>
                <th>PETERNAKAN</th>
                <th>PERIKANAN</th>
                <th>WARUNG HIDUP</th>
                <th>LUMBUNG HIDUP</th>
                <th>TOGA</th>
                <th>TANAMAN KERAS</th>
                <th>TANAMAN LAINNYA</th>
            </tr>
            <tr class="number-row">
                @for ($column = 1; $column <= 20; $column++)
                    <th>{{ $column }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
                <tr>
                    <td>{{ $item['nomor_urut'] ?? '-' }}</td>
                    <td class="left">{{ $item['nama_wilayah'] ?? '-' }}</td>
                    <td>{{ $item['jumlah_kader_pangan'] ?? 0 }}</td>
                    <td>{{ $item['jumlah_kader_sandang'] ?? 0 }}</td>
                    <td>{{ $item['jumlah_kader_tata_laksana_rumah_tangga'] ?? 0 }}</td>
                    <td>{{ $item['jumlah_keluarga_beras'] ?? 0 }}</td>
                    <td>{{ $item['jumlah_keluarga_non_beras'] ?? 0 }}</td>
                    <td>{{ $item['jumlah_peternakan'] ?? 0 }}</td>
                    <td>{{ $item['jumlah_perikanan'] ?? 0 }}</td>
                    <td>{{ $item['jumlah_warung_hidup'] ?? 0 }}</td>
                    <td>{{ $item['jumlah_lumbung_hidup'] ?? 0 }}</td>
                    <td>{{ $item['jumlah_toga'] ?? 0 }}</td>
                    <td>{{ $item['jumlah_tanaman_keras'] ?? 0 }}</td>
                    <td>{{ $item['jumlah_tanaman_lainnya'] ?? 0 }}</td>
                    <td>{{ $item['jumlah_industri_pangan'] ?? 0 }}</td>
                    <td>{{ $item['jumlah_industri_sandang'] ?? 0 }}</td>
                    <td>{{ $item['jumlah_industri_jasa'] ?? 0 }}</td>
                    <td>{{ $item['jumlah_rumah_sehat_layak_huni'] ?? 0 }}</td>
                    <td>{{ $item['jumlah_rumah_tidak_sehat_tidak_layak_huni'] ?? 0 }}</td>
                    <td class="left">{{ $item['keterangan'] ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td>1</td>
                    <td class="left">-</td>
                    @for ($column = 3; $column <= 20; $column++)
                        <td>{{ $column === 20 ? '-' : 0 }}</td>
                    @endfor
                </tr>
            @endforelse
            <tr class="total-row">
                <td></td>
                <td class="left">KEL/DESA/KEC/KAB/PROV/PST</td>
                <td>{{ $totals['jumlah_kader_pangan'] ?? 0 }}</td>
                <td>{{ $totals['jumlah_kader_sandang'] ?? 0 }}</td>
                <td>{{ $totals['jumlah_kader_tata_laksana_rumah_tangga'] ?? 0 }}</td>
                <td>{{ $totals['jumlah_keluarga_beras'] ?? 0 }}</td>
                <td>{{ $totals['jumlah_keluarga_non_beras'] ?? 0 }}</td>
                <td>{{ $totals['jumlah_peternakan'] ?? 0 }}</td>
                <td>{{ $totals['jumlah_perikanan'] ?? 0 }}</td>
                <td>{{ $totals['jumlah_warung_hidup'] ?? 0 }}</td>
                <td>{{ $totals['jumlah_lumbung_hidup'] ?? 0 }}</td>
                <td>{{ $totals['jumlah_toga'] ?? 0 }}</td>
                <td>{{ $totals['jumlah_tanaman_keras'] ?? 0 }}</td>
                <td>{{ $totals['jumlah_tanaman_lainnya'] ?? 0 }}</td>
                <td>{{ $totals['jumlah_industri_pangan'] ?? 0 }}</td>
                <td>{{ $totals['jumlah_industri_sandang'] ?? 0 }}</td>
                <td>{{ $totals['jumlah_industri_jasa'] ?? 0 }}</td>
                <td>{{ $totals['jumlah_rumah_sehat_layak_huni'] ?? 0 }}</td>
                <td>{{ $totals['jumlah_rumah_tidak_sehat_tidak_layak_huni'] ?? 0 }}</td>
                <td></td>
            </tr>
            <tr class="total-row">
                <td></td>
                <td class="left">JUMLAH</td>
                <td>{{ $totals['jumlah_kader_pangan'] ?? 0 }}</td>
                <td>{{ $totals['jumlah_kader_sandang'] ?? 0 }}</td>
                <td>{{ $totals['jumlah_kader_tata_laksana_rumah_tangga'] ?? 0 }}</td>
                <td>{{ $totals['jumlah_keluarga_beras'] ?? 0 }}</td>
                <td>{{ $totals['jumlah_keluarga_non_beras'] ?? 0 }}</td>
                <td>{{ $totals['jumlah_peternakan'] ?? 0 }}</td>
                <td>{{ $totals['jumlah_perikanan'] ?? 0 }}</td>
                <td>{{ $totals['jumlah_warung_hidup'] ?? 0 }}</td>
                <td>{{ $totals['jumlah_lumbung_hidup'] ?? 0 }}</td>
                <td>{{ $totals['jumlah_toga'] ?? 0 }}</td>
                <td>{{ $totals['jumlah_tanaman_keras'] ?? 0 }}</td>
                <td>{{ $totals['jumlah_tanaman_lainnya'] ?? 0 }}</td>
                <td>{{ $totals['jumlah_industri_pangan'] ?? 0 }}</td>
                <td>{{ $totals['jumlah_industri_sandang'] ?? 0 }}</td>
                <td>{{ $totals['jumlah_industri_jasa'] ?? 0 }}</td>
                <td>{{ $totals['jumlah_rumah_sehat_layak_huni'] ?? 0 }}</td>
                <td>{{ $totals['jumlah_rumah_tidak_sehat_tidak_layak_huni'] ?? 0 }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>
</body>
</html>
