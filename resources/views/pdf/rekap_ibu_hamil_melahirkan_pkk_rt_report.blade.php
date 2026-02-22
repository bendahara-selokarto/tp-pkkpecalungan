<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Data/Buku Catatan Ibu Hamil Kelompok PKK RT</title>
    <style>
        @page { margin: 14px 16px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 7px; color: #111827; margin: 0; }
        .lampiran { text-align: right; font-weight: 700; font-size: 10px; margin-bottom: 4px; }
        .title { text-align: center; font-size: 11px; font-weight: 700; margin-bottom: 8px; line-height: 1.35; }
        .meta-grid { width: 100%; margin-bottom: 8px; border-collapse: collapse; }
        .meta-grid td { vertical-align: top; }
        .meta-left, .meta-right { width: 50%; }
        .meta-table { width: 100%; border-collapse: collapse; }
        .meta-table td { padding: 1px 0; }
        .meta-table .label { width: 160px; font-weight: 700; }
        .meta-table .sep { width: 8px; text-align: center; font-weight: 700; }
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
        .left { text-align: left; }
        .center { text-align: center; }
        .note { margin-top: 4px; font-size: 7px; font-weight: 700; }
        .meta-footer { margin-top: 6px; font-size: 7px; }
    </style>
</head>
<body>
    @php
        $scopeLevel = \App\Domains\Wilayah\Enums\ScopeLevel::tryFrom((string) $level);
        $levelLabel = $scopeLevel?->reportLevelLabel() ?? strtoupper((string) $level);
        $metaData = $meta ?? [];
        $totalsData = $totals ?? [];
    @endphp

    <div class="lampiran">LAMPIRAN 4.18b</div>
    <div class="title">
        REKAPITULASI DATA/BUKU CATATAN<br>
        IBU HAMIL, MELAHIRKAN, NIFAS, IBU MENINGGAL, KELAHIRAN BAYI, BAYI MENINGGAL DAN KEMATIAN BALITA DALAM KELOMPOK<br>
        PKK RT
    </div>

    <table class="meta-grid">
        <tr>
            <td class="meta-left">
                <table class="meta-table">
                    <tr>
                        <td class="label">RT/RW/DUS/LING</td>
                        <td class="sep">:</td>
                        <td>{{ $metaData['rt_rw_dus_ling'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">DESA/KELURAHAN</td>
                        <td class="sep">:</td>
                        <td>{{ $metaData['desa_kelurahan'] ?? '-' }}</td>
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
                </table>
            </td>
            <td class="meta-right">
                <table class="meta-table">
                    <tr>
                        <td class="label">Bulan</td>
                        <td class="sep">:</td>
                        <td>{{ $bulan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Tahun</td>
                        <td class="sep">:</td>
                        <td>{{ $tahun ?? '-' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="main-table">
        <thead>
            <tr class="header-group">
                <th rowspan="3">NO.</th>
                <th rowspan="3">NAMA KELOMPOK DASA WISMA</th>
                <th colspan="4">JUMLAH IBU</th>
                <th colspan="6">JUMLAH BAYI</th>
                <th colspan="2">JUMLAH BALITA MENINGGAL</th>
                <th rowspan="3">KETERANGAN</th>
            </tr>
            <tr class="header-sub">
                <th rowspan="2">HAMIL</th>
                <th rowspan="2">MELAHIRKAN</th>
                <th rowspan="2">NIFAS</th>
                <th rowspan="2">MENINGGAL</th>
                <th colspan="2">LAHIR</th>
                <th colspan="2">AKTE KELAHIRAN</th>
                <th colspan="2">MENINGGAL</th>
                <th colspan="2">MENINGGAL</th>
            </tr>
            <tr class="header-mini">
                <th>L</th>
                <th>P</th>
                <th>ADA</th>
                <th>TIDAK ADA</th>
                <th>L</th>
                <th>P</th>
                <th>L</th>
                <th>P</th>
            </tr>
            <tr class="header-number">
                @for ($column = 1; $column <= 15; $column++)
                    <th>{{ $column }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
                <tr>
                    <td class="center">{{ $item['nomor_urut'] }}</td>
                    <td class="left">{{ $item['nama_kelompok_dasa_wisma'] ?? '-' }}</td>
                    <td class="center">{{ $item['jumlah_ibu_hamil'] ?? 0 }}</td>
                    <td class="center">{{ $item['jumlah_ibu_melahirkan'] ?? 0 }}</td>
                    <td class="center">{{ $item['jumlah_ibu_nifas'] ?? 0 }}</td>
                    <td class="center">{{ $item['jumlah_ibu_meninggal'] ?? 0 }}</td>
                    <td class="center">{{ $item['jumlah_bayi_lahir_l'] ?? 0 }}</td>
                    <td class="center">{{ $item['jumlah_bayi_lahir_p'] ?? 0 }}</td>
                    <td class="center">{{ $item['jumlah_akte_kelahiran_ada'] ?? 0 }}</td>
                    <td class="center">{{ $item['jumlah_akte_kelahiran_tidak_ada'] ?? 0 }}</td>
                    <td class="center">{{ $item['jumlah_bayi_meninggal_l'] ?? 0 }}</td>
                    <td class="center">{{ $item['jumlah_bayi_meninggal_p'] ?? 0 }}</td>
                    <td class="center">{{ $item['jumlah_balita_meninggal_l'] ?? 0 }}</td>
                    <td class="center">{{ $item['jumlah_balita_meninggal_p'] ?? 0 }}</td>
                    <td class="left">{{ $item['keterangan'] ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="15" class="center">Data rekap belum tersedia.</td>
                </tr>
            @endforelse
            <tr>
                <td></td>
                <td class="left"><strong>JUMLAH</strong></td>
                <td class="center"><strong>{{ $totalsData['jumlah_ibu_hamil'] ?? 0 }}</strong></td>
                <td class="center"><strong>{{ $totalsData['jumlah_ibu_melahirkan'] ?? 0 }}</strong></td>
                <td class="center"><strong>{{ $totalsData['jumlah_ibu_nifas'] ?? 0 }}</strong></td>
                <td class="center"><strong>{{ $totalsData['jumlah_ibu_meninggal'] ?? 0 }}</strong></td>
                <td class="center"><strong>{{ $totalsData['jumlah_bayi_lahir_l'] ?? 0 }}</strong></td>
                <td class="center"><strong>{{ $totalsData['jumlah_bayi_lahir_p'] ?? 0 }}</strong></td>
                <td class="center"><strong>{{ $totalsData['jumlah_akte_kelahiran_ada'] ?? 0 }}</strong></td>
                <td class="center"><strong>{{ $totalsData['jumlah_akte_kelahiran_tidak_ada'] ?? 0 }}</strong></td>
                <td class="center"><strong>{{ $totalsData['jumlah_bayi_meninggal_l'] ?? 0 }}</strong></td>
                <td class="center"><strong>{{ $totalsData['jumlah_bayi_meninggal_p'] ?? 0 }}</strong></td>
                <td class="center"><strong>{{ $totalsData['jumlah_balita_meninggal_l'] ?? 0 }}</strong></td>
                <td class="center"><strong>{{ $totalsData['jumlah_balita_meninggal_p'] ?? 0 }}</strong></td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <div class="note">* ibu meninggal karena hamil/melahirkan/nifas</div>

    <div class="meta-footer">
        Level: {{ $levelLabel }} | Dicetak oleh: {{ $printedBy?->name ?? '-' }} | Dicetak pada: {{ $printedAt->format('Y-m-d H:i:s') }}
    </div>
</body>
</html>
