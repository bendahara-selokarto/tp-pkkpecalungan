<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Kegiatan PKK Pokja II</title>
    <style>
        @page { margin: 12px 14px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 6px; color: #111827; margin: 0; }
        .lampiran { text-align: right; font-weight: 700; font-size: 9px; margin-bottom: 3px; }
        .title { text-align: center; font-size: 11px; font-weight: 700; margin-bottom: 3px; }
        .meta { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
        .meta td { padding: 1px 4px; }
        .meta .label { width: 70px; font-weight: 700; }
        .meta .sep { width: 8px; text-align: center; font-weight: 700; }
        .pokja { font-size: 9px; font-weight: 700; margin: 3px 0 3px; }
        .main-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .main-table th, .main-table td {
            border: 1px solid #111827;
            padding: 2px 1px;
            text-align: center;
            vertical-align: middle;
            word-break: break-word;
        }
        .main-table th { font-weight: 700; line-height: 1.15; }
        .left { text-align: left; }
        .number-row th { font-size: 5px; }
        .total-row td { font-weight: 700; }
    </style>
</head>
<body>
    @php($totals = is_array($totals ?? null) ? $totals : [])
    <div class="lampiran">LAMPIRAN 4.22</div>
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

    <div class="pokja">POKJA II</div>

    <table class="main-table">
        <thead>
            <tr>
                <th rowspan="4">NO</th>
                <th rowspan="4">NAMA WILAYAH<br>(DUSUN/DESA/KEL./KEC/KAB/KOTA/PROV)</th>
                <th rowspan="4">JML WARGA<br>YANG MASIH 3 (TIGA) BUTA</th>
                <th colspan="8"></th>
                <th colspan="14">PENDIDIKAN KETERAMPILAN</th>
                <th colspan="10">PENGEMBANGAN KEHIDUPAN BERKOPERASI</th>
                <th rowspan="4">KET.</th>
            </tr>
            <tr>
                <th colspan="2">PAKET A</th>
                <th colspan="2">PAKET B</th>
                <th colspan="2">PAKET C</th>
                <th colspan="2">KF</th>
                <th rowspan="3">PAUD<br>SEJENIS</th>
                <th rowspan="3">JUMLAH TAMAN BACAAN/<br>PERPUSTAKAAN</th>
                <th colspan="4">BKB</th>
                <th colspan="2">TUTOR</th>
                <th colspan="3">KADER KHUSUS</th>
                <th colspan="3">JUMLAH KADER YANG SUDAH DILATIH</th>
                <th colspan="8">PRA KOPERASI/USAHA BERSAMA/UP2K</th>
                <th colspan="2">KOPERASI BERBADAN HUKUM</th>
            </tr>
            <tr>
                <th rowspan="2">JML KLP<br>BELAJAR</th>
                <th rowspan="2">WARGA<br>BELAJAR</th>
                <th rowspan="2">JML KLP<br>BELAJAR</th>
                <th rowspan="2">WARGA<br>BELAJAR</th>
                <th rowspan="2">JML KLP<br>BELAJAR</th>
                <th rowspan="2">WARGA<br>BELAJAR</th>
                <th rowspan="2">JML KLP<br>BELAJAR</th>
                <th rowspan="2">WARGA<br>BELAJAR</th>
                <th rowspan="2">JML KLP</th>
                <th rowspan="2">JML IBU<br>PESERTA</th>
                <th rowspan="2">JML APE<br>(SET)</th>
                <th rowspan="2">JML KLP<br>SIMULASI</th>
                <th rowspan="2">KF</th>
                <th rowspan="2">PAUD<br>SEJENIS</th>
                <th rowspan="2">BKB</th>
                <th rowspan="2">KOPERASI</th>
                <th rowspan="2">KETERAMPILAN</th>
                <th rowspan="2">LP3 PKK</th>
                <th rowspan="2">TPK 3 PKK</th>
                <th rowspan="2">DAMAS PKK</th>
                <th colspan="2">PEMULA</th>
                <th colspan="2">MADYA</th>
                <th colspan="2">UTAMA</th>
                <th colspan="2">MANDIRI</th>
                <th rowspan="2">JML KLP</th>
                <th rowspan="2">JML ANGGOTA</th>
            </tr>
            <tr>
                <th>JML KLP</th>
                <th>PESERTA</th>
                <th>JML KLP</th>
                <th>PESERTA</th>
                <th>JML KLP</th>
                <th>PESERTA</th>
                <th>JML KLP</th>
                <th>PESERTA</th>
            </tr>
            <tr class="number-row">
                @for ($column = 1; $column <= 36; $column++)
                    <th>{{ $column }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
                <tr>
                    <td>{{ $item['nomor_urut'] ?? '-' }}</td>
                    <td class="left">{{ $item['nama_wilayah'] ?? '-' }}</td>
                    <td>{{ $item['jumlah_warga_tiga_buta'] ?? 0 }}</td>
                    <td>{{ $item['paket_a_klp'] ?? 0 }}</td>
                    <td>{{ $item['paket_a_warga'] ?? 0 }}</td>
                    <td>{{ $item['paket_b_klp'] ?? 0 }}</td>
                    <td>{{ $item['paket_b_warga'] ?? 0 }}</td>
                    <td>{{ $item['paket_c_klp'] ?? 0 }}</td>
                    <td>{{ $item['paket_c_warga'] ?? 0 }}</td>
                    <td>{{ $item['kf_klp'] ?? 0 }}</td>
                    <td>{{ $item['kf_warga'] ?? 0 }}</td>
                    <td>{{ $item['paud_klp'] ?? 0 }}</td>
                    <td>{{ $item['taman_baca'] ?? 0 }}</td>
                    <td>{{ $item['bkb_klp'] ?? 0 }}</td>
                    <td>{{ $item['bkb_ibu_peserta'] ?? 0 }}</td>
                    <td>{{ $item['bkb_ape_set'] ?? 0 }}</td>
                    <td>{{ $item['bkb_kelompok_simulasi'] ?? 0 }}</td>
                    <td>{{ $item['tutor_kf'] ?? 0 }}</td>
                    <td>{{ $item['tutor_paud'] ?? 0 }}</td>
                    <td>{{ $item['kader_bkb'] ?? 0 }}</td>
                    <td>{{ $item['kader_koperasi'] ?? 0 }}</td>
                    <td>{{ $item['kader_keterampilan'] ?? 0 }}</td>
                    <td>{{ $item['pelatihan_lp3'] ?? 0 }}</td>
                    <td>{{ $item['pelatihan_tpk_3_pkk'] ?? 0 }}</td>
                    <td>{{ $item['pelatihan_damas'] ?? 0 }}</td>
                    <td>{{ $item['pra_koperasi_pemula_klp'] ?? 0 }}</td>
                    <td>{{ $item['pra_koperasi_pemula_peserta'] ?? 0 }}</td>
                    <td>{{ $item['pra_koperasi_madya_klp'] ?? 0 }}</td>
                    <td>{{ $item['pra_koperasi_madya_peserta'] ?? 0 }}</td>
                    <td>{{ $item['pra_koperasi_utama_klp'] ?? 0 }}</td>
                    <td>{{ $item['pra_koperasi_utama_peserta'] ?? 0 }}</td>
                    <td>{{ $item['pra_koperasi_mandiri_klp'] ?? 0 }}</td>
                    <td>{{ $item['pra_koperasi_mandiri_peserta'] ?? 0 }}</td>
                    <td>{{ $item['koperasi_berbadan_hukum_klp'] ?? 0 }}</td>
                    <td>{{ $item['koperasi_berbadan_hukum_anggota'] ?? 0 }}</td>
                    <td class="left">{{ $item['keterangan'] ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td>1</td>
                    <td class="left">-</td>
                    @for ($column = 3; $column <= 36; $column++)
                        <td>{{ $column === 36 ? '-' : 0 }}</td>
                    @endfor
                </tr>
            @endforelse
            <tr class="total-row">
                <td></td>
                <td class="left">JUMLAH</td>
                <td>{{ $totals['jumlah_warga_tiga_buta'] ?? 0 }}</td>
                <td>{{ $totals['paket_a_klp'] ?? 0 }}</td>
                <td>{{ $totals['paket_a_warga'] ?? 0 }}</td>
                <td>{{ $totals['paket_b_klp'] ?? 0 }}</td>
                <td>{{ $totals['paket_b_warga'] ?? 0 }}</td>
                <td>{{ $totals['paket_c_klp'] ?? 0 }}</td>
                <td>{{ $totals['paket_c_warga'] ?? 0 }}</td>
                <td>{{ $totals['kf_klp'] ?? 0 }}</td>
                <td>{{ $totals['kf_warga'] ?? 0 }}</td>
                <td>{{ $totals['paud_klp'] ?? 0 }}</td>
                <td>{{ $totals['taman_baca'] ?? 0 }}</td>
                <td>{{ $totals['bkb_klp'] ?? 0 }}</td>
                <td>{{ $totals['bkb_ibu_peserta'] ?? 0 }}</td>
                <td>{{ $totals['bkb_ape_set'] ?? 0 }}</td>
                <td>{{ $totals['bkb_kelompok_simulasi'] ?? 0 }}</td>
                <td>{{ $totals['tutor_kf'] ?? 0 }}</td>
                <td>{{ $totals['tutor_paud'] ?? 0 }}</td>
                <td>{{ $totals['kader_bkb'] ?? 0 }}</td>
                <td>{{ $totals['kader_koperasi'] ?? 0 }}</td>
                <td>{{ $totals['kader_keterampilan'] ?? 0 }}</td>
                <td>{{ $totals['pelatihan_lp3'] ?? 0 }}</td>
                <td>{{ $totals['pelatihan_tpk_3_pkk'] ?? 0 }}</td>
                <td>{{ $totals['pelatihan_damas'] ?? 0 }}</td>
                <td>{{ $totals['pra_koperasi_pemula_klp'] ?? 0 }}</td>
                <td>{{ $totals['pra_koperasi_pemula_peserta'] ?? 0 }}</td>
                <td>{{ $totals['pra_koperasi_madya_klp'] ?? 0 }}</td>
                <td>{{ $totals['pra_koperasi_madya_peserta'] ?? 0 }}</td>
                <td>{{ $totals['pra_koperasi_utama_klp'] ?? 0 }}</td>
                <td>{{ $totals['pra_koperasi_utama_peserta'] ?? 0 }}</td>
                <td>{{ $totals['pra_koperasi_mandiri_klp'] ?? 0 }}</td>
                <td>{{ $totals['pra_koperasi_mandiri_peserta'] ?? 0 }}</td>
                <td>{{ $totals['koperasi_berbadan_hukum_klp'] ?? 0 }}</td>
                <td>{{ $totals['koperasi_berbadan_hukum_anggota'] ?? 0 }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>
</body>
</html>
