<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Data/Buku Catatan Ibu Hamil dan Kelahiran</title>
    <style>
        @page { margin: 14px 16px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 7px; color: #111827; margin: 0; }
        .lampiran { text-align: right; font-weight: 700; font-size: 10px; margin-bottom: 4px; }
        .title { text-align: center; font-size: 11px; font-weight: 700; margin-bottom: 8px; line-height: 1.35; }
        .meta-wrap { width: 100%; margin-bottom: 8px; border-collapse: collapse; }
        .meta-wrap td { padding: 1px 4px; vertical-align: top; }
        .meta-wrap .label { width: 200px; font-weight: 700; }
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
        .notes-wrap {
            margin-top: 8px;
            border-collapse: collapse;
            width: 60%;
        }
        .notes-wrap td { padding: 1px 2px; vertical-align: top; }
        .notes-title { font-weight: 700; margin-top: 6px; }
        .meta-footer { margin-top: 8px; font-size: 7px; }
    </style>
</head>
<body>
    @php
        $scopeLevel = \App\Domains\Wilayah\Enums\ScopeLevel::tryFrom((string) $level);
        $levelLabel = $scopeLevel?->reportLevelLabel() ?? strtoupper((string) $level);
        $metaData = $meta ?? [];
        $notesData = $notes ?? [];
        $totalsData = $totals ?? [];
    @endphp

    <div class="lampiran">LAMPIRAN 4.18a</div>
    <div class="title">
        REKAPITULASI DATA/BUKU CATATAN<br>
        IBU HAMIL, MELAHIRKAN, NIFAS, IBU MENINGGAL, KELAHIRAN BAYI, BAYI MENINGGAL DAN<br>
        KEMATIAN BALITA DALAM KELOMPOK DASAWISMA
    </div>

    <table class="meta-wrap">
        <tr>
            <td class="label">KELOMPOK DASAWISMA</td>
            <td class="sep">:</td>
            <td>{{ $metaData['kelompok_dasawisma'] ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">KELOMPOK PKK RT</td>
            <td class="sep">:</td>
            <td>{{ $metaData['kelompok_pkk_rt'] ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">KELOMPOK PKK RW</td>
            <td class="sep">:</td>
            <td>{{ $metaData['kelompok_pkk_rw'] ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">DUSUN/LINGKUNGAN</td>
            <td class="sep">:</td>
            <td>{{ $metaData['dusun_lingkungan'] ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">DESA/KELURAHAN</td>
            <td class="sep">:</td>
            <td>{{ $metaData['desa_kelurahan'] ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">BULAN</td>
            <td class="sep">:</td>
            <td>{{ $bulan ?? '-' }}</td>
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
                <th rowspan="3">NO.</th>
                <th rowspan="3">NAMA IBU</th>
                <th rowspan="3">NAMA SUAMI</th>
                <th rowspan="3">STATUS<br>(HAMIL/MELAHIRKAN/NIFAS)</th>
                <th colspan="6">CATATAN KELAHIRAN</th>
                <th colspan="7">CATATAN KEMATIAN</th>
            </tr>
            <tr class="header-sub">
                <th rowspan="2">NAMA BAYI</th>
                <th colspan="2">JENIS KELAMIN</th>
                <th rowspan="2">TGL. LAHIR</th>
                <th colspan="2">AKTE KELAHIRAN</th>
                <th rowspan="2">NAMA IBU/BAYI/BALITA</th>
                <th rowspan="2">STATUS<br>(IBU/BALITA/BAYI)</th>
                <th colspan="2">JENIS KELAMIN</th>
                <th rowspan="2">TGL. MENINGGAL</th>
                <th rowspan="2">SEBAB MENINGGAL</th>
                <th rowspan="2">KETERANGAN</th>
            </tr>
            <tr class="header-mini">
                <th>L</th>
                <th>P</th>
                <th>ADA</th>
                <th>TIDAK ADA</th>
                <th>L</th>
                <th>P</th>
            </tr>
            <tr class="header-number">
                @for ($column = 1; $column <= 17; $column++)
                    <th>{{ $column }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
                <tr>
                    <td class="center">{{ $item['nomor_urut'] }}</td>
                    <td class="left">{{ $item['nama_ibu'] ?? '-' }}</td>
                    <td class="left">{{ $item['nama_suami'] ?? '-' }}</td>
                    <td class="center">{{ $item['status_ibu'] ?? '-' }}</td>
                    <td class="left">{{ $item['nama_bayi'] ?? '-' }}</td>
                    <td class="center">{{ $item['kelahiran_l'] ?? 0 }}</td>
                    <td class="center">{{ $item['kelahiran_p'] ?? 0 }}</td>
                    <td class="center">{{ $item['tanggal_lahir'] ?? '-' }}</td>
                    <td class="center">{{ $item['akta_ada'] ?? 0 }}</td>
                    <td class="center">{{ $item['akta_tidak_ada'] ?? 0 }}</td>
                    <td class="left">{{ $item['catatan_kematian_nama'] ?? '-' }}</td>
                    <td class="center">{{ $item['catatan_kematian_status'] ?? '-' }}</td>
                    <td class="center">{{ $item['kematian_l'] ?? 0 }}</td>
                    <td class="center">{{ $item['kematian_p'] ?? 0 }}</td>
                    <td class="center">{{ $item['tanggal_meninggal'] ?? '-' }}</td>
                    <td class="left">{{ $item['sebab_meninggal'] ?? '-' }}</td>
                    <td class="left">{{ $item['keterangan'] ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="17" class="center">Data rekap belum tersedia.</td>
                </tr>
            @endforelse
            <tr>
                <td></td>
                <td class="left" colspan="2"><strong>JUMLAH</strong></td>
                <td></td>
                <td></td>
                <td class="center"><strong>{{ $totalsData['kelahiran_l'] ?? 0 }}</strong></td>
                <td class="center"><strong>{{ $totalsData['kelahiran_p'] ?? 0 }}</strong></td>
                <td></td>
                <td class="center"><strong>{{ $totalsData['akta_ada'] ?? 0 }}</strong></td>
                <td class="center"><strong>{{ $totalsData['akta_tidak_ada'] ?? 0 }}</strong></td>
                <td></td>
                <td></td>
                <td class="center"><strong>{{ $totalsData['kematian_l'] ?? 0 }}</strong></td>
                <td class="center"><strong>{{ $totalsData['kematian_p'] ?? 0 }}</strong></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <div class="notes-title">CATATAN :</div>
    <table class="notes-wrap">
        <tr>
            <td>1. jumlah ibu hamil</td>
            <td>:</td>
            <td>{{ $notesData['jumlah_ibu_hamil'] ?? 0 }} Orang</td>
        </tr>
        <tr>
            <td>2. jumlah ibu melahirkan</td>
            <td>:</td>
            <td>{{ $notesData['jumlah_ibu_melahirkan'] ?? 0 }} Orang</td>
        </tr>
        <tr>
            <td>3. jumlah ibu nifas</td>
            <td>:</td>
            <td>{{ $notesData['jumlah_ibu_nifas'] ?? 0 }} Orang</td>
        </tr>
        <tr>
            <td>4. jumlah ibu meninggal *</td>
            <td>:</td>
            <td>{{ $notesData['jumlah_ibu_meninggal'] ?? 0 }} Orang</td>
        </tr>
        <tr>
            <td>5. jumlah bayi lahir</td>
            <td>:</td>
            <td>{{ $notesData['jumlah_bayi_lahir'] ?? 0 }} Orang</td>
        </tr>
        <tr>
            <td>6. jumlah bayi meninggal</td>
            <td>:</td>
            <td>{{ $notesData['jumlah_bayi_meninggal'] ?? 0 }} Orang</td>
        </tr>
        <tr>
            <td>7. jumlah kematian balita</td>
            <td>:</td>
            <td>{{ $notesData['jumlah_kematian_balita'] ?? 0 }} Orang</td>
        </tr>
        <tr>
            <td colspan="3">* ibu meninggal karena hamil/melahirkan/nifas</td>
        </tr>
    </table>

    <div class="meta-footer">
        Level: {{ $levelLabel }} | Dicetak oleh: {{ $printedBy?->name ?? '-' }} | Dicetak pada: {{ $printedAt->format('Y-m-d H:i:s') }}
    </div>
</body>
</html>
