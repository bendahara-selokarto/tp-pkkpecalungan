<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Catatan Data dan Kegiatan Warga Kelompok PKK Dusun/Lingkungan</title>
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
        .main-table { width: 100%; border-collapse: collapse; }
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

    <div class="lampiran">LAMPIRAN 4.16d</div>
    <div class="title">CATATAN DATA DAN KEGIATAN WARGA KELOMPOK PKK DUSUN/LINGKUNGAN</div>

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
        <colgroup>
            <col style="width: 20px;"> {{-- 1. NO --}}
            <col style="width: 40px;"> {{-- 2. NOMOR RW --}}
            <col style="width: 25px;"> {{-- 3. JML RT --}}
            <col style="width: 25px;"> {{-- 4. JML DASAWISMA --}}
            <col style="width: 25px;"> {{-- 5. JML KRT --}}
            <col style="width: 25px;"> {{-- 6. JML KK --}}
            <col style="width: 18px;"> {{-- 7. TOTAL L --}}
            <col style="width: 18px;"> {{-- 8. TOTAL P --}}
            <col style="width: 18px;"> {{-- 9. BALITA L --}}
            <col style="width: 18px;"> {{-- 10. BALITA P --}}
            <col style="width: 18px;"> {{-- 11. PUS --}}
            <col style="width: 18px;"> {{-- 12. WUS --}}
            <col style="width: 18px;"> {{-- 13. IBU HAMIL --}}
            <col style="width: 18px;"> {{-- 14. IBU MENYUSUI --}}
            <col style="width: 18px;"> {{-- 15. LANSIA --}}
            <col style="width: 18px;"> {{-- 16. 3 BUTA L --}}
            <col style="width: 18px;"> {{-- 17. 3 BUTA P --}}
            <col style="width: 20px;"> {{-- 18. SEHAT LAYAK HUNI --}}
            <col style="width: 20px;"> {{-- 19. TIDAK SEHAT LAYAK HUNI --}}
            <col style="width: 20px;"> {{-- 20. MEMILIKI TTMP. PEMB. SAMPAH --}}
            <col style="width: 20px;"> {{-- 21. MEMILIKI SPAL DAN PENYERAPAN AIR --}}
            <col style="width: 20px;"> {{-- 22. PDAM --}}
            <col style="width: 20px;"> {{-- 23. SUMUR --}}
            <col style="width: 20px;"> {{-- 24. SUNGAI --}}
            <col style="width: 20px;"> {{-- 25. DLL --}}
            <col style="width: 30px;"> {{-- 26. JUMLAH SARANA MCK --}}
            <col style="width: 25px;"> {{-- 27. BERAS --}}
            <col style="width: 25px;"> {{-- 28. NON BERAS --}}
            <col style="width: 25px;"> {{-- 29. UP2K --}}
            <col style="width: 25px;"> {{-- 30. PEMANFAATAN TANAH PEKARANGAN --}}
            <col style="width: 25px;"> {{-- 31. INDUSTRI RUMAH TANGGA --}}
            <col style="width: 25px;"> {{-- 32. KESEHATAN LINGKUNGAN --}}
            <col style="width: 60px;"> {{-- 33. KET --}}
        </colgroup>
        <thead>
            <tr class="header-group">
                <th rowspan="3">NO</th>
                <th rowspan="3">NOMOR RW</th>
                <th rowspan="3">JML RT</th>
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
                @for ($column = 1; $column <= 33; $column++)
                    <th>{{ $column }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
                <tr>
                    <td class="center">{{ $item['nomor_urut'] }}</td>
                    <td class="center">{{ $item['nomor_rw'] ?? '-' }}</td>
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
                    <td colspan="33" class="center">Data rekap belum tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @include('pdf.partials._report_footer')

    <div class="meta-footer">
        Level: {{ $levelLabel }} | 
    </div>
    @include('pdf.partials._report_metadata')
</body>
</html>
