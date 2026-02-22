<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Umum PKK</title>
    <style>
        @page { margin: 14px 16px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 7px; color: #111827; margin: 0; }
        .lampiran { text-align: right; font-weight: 700; font-size: 10px; margin-bottom: 4px; }
        .title { text-align: center; font-size: 12px; font-weight: 700; margin-bottom: 8px; }
        .meta-wrap { width: 100%; margin-bottom: 8px; border-collapse: collapse; }
        .meta-wrap td { padding: 1px 0; vertical-align: top; }
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
        $metaData = $meta ?? [];
        $totalsData = $totals ?? [];
        $tpPkkTotals = $tpPkkDesaKelurahanTotals ?? [];
    @endphp

    <div class="lampiran">LAMPIRAN 4.20a</div>
    <div class="title">DATA UMUM PKK</div>

    <table class="meta-wrap">
        <tr>
            <td class="label">TP. PKK DESA/KEL</td>
            <td class="sep">:</td>
            <td>{{ $metaData['tp_pkk_desa_kel'] ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">KECAMATAN</td>
            <td class="sep">:</td>
            <td>{{ $metaData['kecamatan'] ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">KAB/KOTA</td>
            <td class="sep">:</td>
            <td>{{ $metaData['kab_kota'] ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">PROVINSI</td>
            <td class="sep">:</td>
            <td>{{ $metaData['provinsi'] ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">TAHUN</td>
            <td class="sep">:</td>
            <td>{{ $tahun ?? '-' }}</td>
        </tr>
    </table>

    <table class="main-table">
        <thead>
            <tr class="header-group">
                <th rowspan="3">NO</th>
                <th rowspan="3">NAMA DUSUN LINGKUNGAN ATAU SEBUTAN LAIN</th>
                <th colspan="3">JUMLAH KELOMPOK</th>
                <th colspan="2">JUMLAH</th>
                <th colspan="2">JUMLAH JIWA</th>
                <th colspan="6">JUMLAH KADER</th>
                <th colspan="4">JUMLAH TENAGA SEKRETARIAT</th>
                <th rowspan="3">KETERANGAN</th>
            </tr>
            <tr class="header-sub">
                <th rowspan="2">PKK RW</th>
                <th rowspan="2">PKK RT</th>
                <th rowspan="2">DASA WISMA</th>
                <th rowspan="2">KRT</th>
                <th rowspan="2">KK</th>
                <th rowspan="2">L</th>
                <th rowspan="2">P</th>
                <th colspan="2">ANGGOTA TP. PKK</th>
                <th colspan="2">UMUM</th>
                <th colspan="2">KHUSUS</th>
                <th colspan="2">HONORER</th>
                <th colspan="2">BANTUAN</th>
            </tr>
            <tr class="header-mini">
                <th>L</th>
                <th>P</th>
                <th>L</th>
                <th>P</th>
                <th>L</th>
                <th>P</th>
                <th>L</th>
                <th>P</th>
                <th>L</th>
                <th>P</th>
            </tr>
            <tr class="header-number">
                @for ($column = 1; $column <= 20; $column++)
                    <th>{{ $column }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
                <tr>
                    <td class="center">{{ $item['nomor_urut'] }}</td>
                    <td class="left">{{ $item['nama_dusun_lingkungan_atau_sebutan_lain'] ?? '-' }}</td>
                    <td class="center">{{ $item['jumlah_pkk_rw'] ?? 0 }}</td>
                    <td class="center">{{ $item['jumlah_pkk_rt'] ?? 0 }}</td>
                    <td class="center">{{ $item['jumlah_dasa_wisma'] ?? 0 }}</td>
                    <td class="center">{{ $item['jumlah_krt'] ?? 0 }}</td>
                    <td class="center">{{ $item['jumlah_kk'] ?? 0 }}</td>
                    <td class="center">{{ $item['jumlah_jiwa_l'] ?? 0 }}</td>
                    <td class="center">{{ $item['jumlah_jiwa_p'] ?? 0 }}</td>
                    <td class="center">{{ $item['jumlah_kader_anggota_tp_pkk_l'] ?? 0 }}</td>
                    <td class="center">{{ $item['jumlah_kader_anggota_tp_pkk_p'] ?? 0 }}</td>
                    <td class="center">{{ $item['jumlah_kader_umum_l'] ?? 0 }}</td>
                    <td class="center">{{ $item['jumlah_kader_umum_p'] ?? 0 }}</td>
                    <td class="center">{{ $item['jumlah_kader_khusus_l'] ?? 0 }}</td>
                    <td class="center">{{ $item['jumlah_kader_khusus_p'] ?? 0 }}</td>
                    <td class="center">{{ $item['jumlah_tenaga_sekretariat_honorer_l'] ?? 0 }}</td>
                    <td class="center">{{ $item['jumlah_tenaga_sekretariat_honorer_p'] ?? 0 }}</td>
                    <td class="center">{{ $item['jumlah_tenaga_sekretariat_bantuan_l'] ?? 0 }}</td>
                    <td class="center">{{ $item['jumlah_tenaga_sekretariat_bantuan_p'] ?? 0 }}</td>
                    <td class="left">{{ $item['keterangan'] ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="20" class="center">Data umum PKK belum tersedia.</td>
                </tr>
            @endforelse
            <tr>
                <td></td>
                <td class="left"><strong>TP PKK DESA/KELURAHAN</strong></td>
                <td colspan="7"></td>
                <td class="center"><strong>{{ $tpPkkTotals['jumlah_kader_anggota_tp_pkk_l'] ?? 0 }}</strong></td>
                <td class="center"><strong>{{ $tpPkkTotals['jumlah_kader_anggota_tp_pkk_p'] ?? 0 }}</strong></td>
                <td class="center"><strong>{{ $tpPkkTotals['jumlah_kader_umum_l'] ?? 0 }}</strong></td>
                <td class="center"><strong>{{ $tpPkkTotals['jumlah_kader_umum_p'] ?? 0 }}</strong></td>
                <td class="center"><strong>{{ $tpPkkTotals['jumlah_kader_khusus_l'] ?? 0 }}</strong></td>
                <td class="center"><strong>{{ $tpPkkTotals['jumlah_kader_khusus_p'] ?? 0 }}</strong></td>
                <td class="center"><strong>{{ $tpPkkTotals['jumlah_tenaga_sekretariat_honorer_l'] ?? 0 }}</strong></td>
                <td class="center"><strong>{{ $tpPkkTotals['jumlah_tenaga_sekretariat_honorer_p'] ?? 0 }}</strong></td>
                <td class="center"><strong>{{ $tpPkkTotals['jumlah_tenaga_sekretariat_bantuan_l'] ?? 0 }}</strong></td>
                <td class="center"><strong>{{ $tpPkkTotals['jumlah_tenaga_sekretariat_bantuan_p'] ?? 0 }}</strong></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td class="left"><strong>JUMLAH</strong></td>
                <td class="center"><strong>{{ $totalsData['jumlah_pkk_rw'] ?? 0 }}</strong></td>
                <td class="center"><strong>{{ $totalsData['jumlah_pkk_rt'] ?? 0 }}</strong></td>
                <td class="center"><strong>{{ $totalsData['jumlah_dasa_wisma'] ?? 0 }}</strong></td>
                <td class="center"><strong>{{ $totalsData['jumlah_krt'] ?? 0 }}</strong></td>
                <td class="center"><strong>{{ $totalsData['jumlah_kk'] ?? 0 }}</strong></td>
                <td class="center"><strong>{{ $totalsData['jumlah_jiwa_l'] ?? 0 }}</strong></td>
                <td class="center"><strong>{{ $totalsData['jumlah_jiwa_p'] ?? 0 }}</strong></td>
                <td class="center"><strong>{{ $totalsData['jumlah_kader_anggota_tp_pkk_l'] ?? 0 }}</strong></td>
                <td class="center"><strong>{{ $totalsData['jumlah_kader_anggota_tp_pkk_p'] ?? 0 }}</strong></td>
                <td class="center"><strong>{{ $totalsData['jumlah_kader_umum_l'] ?? 0 }}</strong></td>
                <td class="center"><strong>{{ $totalsData['jumlah_kader_umum_p'] ?? 0 }}</strong></td>
                <td class="center"><strong>{{ $totalsData['jumlah_kader_khusus_l'] ?? 0 }}</strong></td>
                <td class="center"><strong>{{ $totalsData['jumlah_kader_khusus_p'] ?? 0 }}</strong></td>
                <td class="center"><strong>{{ $totalsData['jumlah_tenaga_sekretariat_honorer_l'] ?? 0 }}</strong></td>
                <td class="center"><strong>{{ $totalsData['jumlah_tenaga_sekretariat_honorer_p'] ?? 0 }}</strong></td>
                <td class="center"><strong>{{ $totalsData['jumlah_tenaga_sekretariat_bantuan_l'] ?? 0 }}</strong></td>
                <td class="center"><strong>{{ $totalsData['jumlah_tenaga_sekretariat_bantuan_p'] ?? 0 }}</strong></td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <div class="meta-footer">
        Level: {{ $levelLabel }} | Dicetak oleh: {{ $printedBy?->name ?? '-' }} | Dicetak pada: {{ $printedAt->format('Y-m-d H:i:s') }}
    </div>
</body>
</html>
