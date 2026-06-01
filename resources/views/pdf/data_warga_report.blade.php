<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Warga TP PKK</title>
    <style>
        @page { margin: 20px 22px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #111827; margin: 0; }
        .sheet { page-break-after: always; }
        .sheet:last-child { page-break-after: auto; }
        .lampiran { text-align: right; font-weight: 700; margin-bottom: 8px; }
        .title { text-align: center; font-size: 13px; font-weight: 700; margin-bottom: 8px; }
        .header-table, .main-table { width: 100%; border-collapse: collapse; }
        .header-table td { padding: 3px 0; vertical-align: top; }
        .header-label { width: 170px; font-weight: 700; }
        .header-sep { width: 12px; text-align: center; font-weight: 700; }
        .main-table th, .main-table td { border: 1px solid #111827; padding: 3px; vertical-align: top; word-break: break-word; }
        .main-table th { font-weight: 700; text-align: center; font-size: 8px; }
        .center { text-align: center; }
        .meta { margin-top: 8px; font-size: 8px; }
        .empty { text-align: center; padding: 8px; }
    </style>
</head>
<body>
    @php
        $records = $items->isNotEmpty() ? $items : collect([null]);
    @endphp

    @foreach ($records as $recordIndex => $item)
        @php
            $anggotaRows = $item?->anggota ?? collect();
        @endphp

        <section class="sheet">
            <div class="lampiran">LAMPIRAN 4.14.1a</div>
            <div class="title">DAFTAR WARGA TP PKK</div>

            <table class="header-table">
                <tr>
                    <td class="header-label">Dasa Wisma</td>
                    <td class="header-sep">:</td>
                    <td>{{ $item?->dasawisma ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="header-label">Nama Kepala Rumah Tangga</td>
                    <td class="header-sep">:</td>
                    <td>{{ $item?->nama_kepala_keluarga ?? '-' }}</td>
                </tr>
            </table>

            <table class="main-table">
                <colgroup>
                    <col style="width: 25px;"> <!-- 1: NO -->
                    <col style="width: 55px;"> <!-- 2: REG -->
                    <col style="width: 55px;"> <!-- 3: KTP -->
                    <col style="width: 80px;"> <!-- 4: NAMA -->
                    <col style="width: 45px;"> <!-- 5: JAB -->
                    <col style="width: 20px;"> <!-- 6: L -->
                    <col style="width: 20px;"> <!-- 7: P -->
                    <col style="width: 60px;"> <!-- 8: TEMP -->
                    <col style="width: 60px;"> <!-- 9: TGL -->
                    <col style="width: 30px;"> <!-- 10: UMUR -->
                    <col style="width: 60px;"> <!-- 11: STATUS PERK -->
                    <col style="width: 60px;"> <!-- 12: STATUS KEL -->
                    <col style="width: 50px;"> <!-- 13: AGAMA -->
                    <col style="width: 70px;"> <!-- 14: ALAMAT -->
                    <col style="width: 60px;"> <!-- 15: DESA -->
                    <col style="width: 60px;"> <!-- 16: PEND -->
                    <col style="width: 60px;"> <!-- 17: PEK -->
                    <col style="width: 35px;"> <!-- 18: KB -->
                    <col style="width: 35px;"> <!-- 19: POS -->
                    <col style="width: 35px;"> <!-- 20: BKB -->
                    <col style="width: 35px;"> <!-- 21: TAB -->
                    <col style="width: 25px;"> <!-- 22: IKUT -->
                    <col style="width: 45px;"> <!-- 23: JENIS -->
                    <col style="width: 40px;"> <!-- 24: PAUD -->
                    <col style="width: 40px;"> <!-- 25: KOP -->
                </colgroup>
                <thead>
                    <tr>
                        <th rowspan="2">NO</th>
                        <th rowspan="2">NO. REGISTRASI</th>
                        <th rowspan="2">NO. KTP/KK</th>
                        <th rowspan="2">NAMA</th>
                        <th rowspan="2">JABATAN</th>
                        <th colspan="2">JENIS KELAMIN</th>
                        <th rowspan="2">TEMPAT LAHIR</th>
                        <th rowspan="2">TGL. LAHIR</th>
                        <th rowspan="2">UMUR (TH)</th>
                        <th rowspan="2">STATUS PERKAWINAN</th>
                        <th rowspan="2">STATUS DALAM KELUARGA</th>
                        <th rowspan="2">AGAMA</th>
                        <th rowspan="2">ALAMAT</th>
                        <th rowspan="2">DESA/KEL/SEJENIS</th>
                        <th rowspan="2">PENDIDIKAN</th>
                        <th rowspan="2">PEKERJAAN</th>
                        <th rowspan="2">AKSEPTOR KB</th>
                        <th rowspan="2">AKTIF POSYANDU</th>
                        <th rowspan="2">IKUT BKB</th>
                        <th rowspan="2">MEMILIKI TABUNGAN</th>
                        <th colspan="2">KELOMPOK BELAJAR</th>
                        <th rowspan="2">MENGIKUTI PAUD/SEJENIS</th>
                        <th rowspan="2">IKUT KEGIATAN KOPERASI</th>
                    </tr>
                    <tr>
                        <th>L</th>
                        <th>P</th>
                        <th>IKUT</th>
                        <th>JENIS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($anggotaRows as $index => $anggota)
                        <tr>
                            <td class="center">{{ $index + 1 }}</td>
                            <td>{{ $anggota->nomor_registrasi ?: '-' }}</td>
                            <td>{{ $anggota->nomor_ktp_kk ?: '-' }}</td>
                            <td>{{ $anggota->nama ?: '-' }}</td>
                            <td>{{ $anggota->jabatan ?: '-' }}</td>
                            <td class="center">{{ $anggota->jenis_kelamin === 'L' ? 'Y' : '-' }}</td>
                            <td class="center">{{ $anggota->jenis_kelamin === 'P' ? 'Y' : '-' }}</td>
                            <td>{{ $anggota->tempat_lahir ?: '-' }}</td>
                            <td class="center">{{ $anggota->tanggal_lahir ? \Illuminate\Support\Carbon::parse($anggota->tanggal_lahir)->format('d-m-Y') : '-' }}</td>
                            <td class="center">{{ $anggota->umur_tahun ?? '-' }}</td>
                            <td>{{ $anggota->status_perkawinan ?: '-' }}</td>
                            <td>{{ $anggota->status_dalam_keluarga ?: '-' }}</td>
                            <td>{{ $anggota->agama ?: '-' }}</td>
                            <td>{{ $anggota->alamat ?: '-' }}</td>
                            <td>{{ $anggota->desa_kel_sejenis ?: '-' }}</td>
                            <td>{{ $anggota->pendidikan ?: '-' }}</td>
                            <td>{{ $anggota->pekerjaan ?: '-' }}</td>
                            <td class="center">{{ $anggota->akseptor_kb ? 'Ya' : '-' }}</td>
                            <td class="center">{{ $anggota->aktif_posyandu ? 'Ya' : '-' }}</td>
                            <td class="center">{{ $anggota->ikut_bkb ? 'Ya' : '-' }}</td>
                            <td class="center">{{ $anggota->memiliki_tabungan ? 'Ya' : '-' }}</td>
                            <td class="center">{{ $anggota->ikut_kelompok_belajar ? 'Ya' : '-' }}</td>
                            <td>{{ $anggota->jenis_kelompok_belajar ?: '-' }}</td>
                            <td class="center">{{ $anggota->ikut_paud ? 'Ya' : '-' }}</td>
                            <td class="center">{{ $anggota->ikut_koperasi ? 'Ya' : '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="25" class="empty">Data anggota warga belum tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @include('pdf.partials._report_footer')

            <div class="footer-meta">
                Wilayah: {{ $areaName }} | 
                Tahun Anggaran: {{ $budgetYearLabel ?? '-' }} | 
                 | 
                Dicetak pada: {{ $printedAt->format('Y-m-d H:i:s') }}
            </div>
        </section>
    @endforeach
    @include('pdf.partials._report_metadata')
</body>
</html>
