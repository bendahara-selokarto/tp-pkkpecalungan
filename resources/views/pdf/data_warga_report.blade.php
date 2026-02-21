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
        .header-table, .main-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
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
                <thead>
                    <tr>
                        <th rowspan="2" style="width: 20px;">NO</th>
                        <th rowspan="2" style="width: 54px;">NO. REGISTRASI</th>
                        <th rowspan="2" style="width: 54px;">NO. KTP/KK</th>
                        <th rowspan="2" style="width: 74px;">NAMA</th>
                        <th rowspan="2" style="width: 44px;">JABATAN</th>
                        <th colspan="2" style="width: 32px;">JENIS KELAMIN</th>
                        <th rowspan="2" style="width: 50px;">TEMPAT LAHIR</th>
                        <th rowspan="2" style="width: 48px;">TGL. LAHIR</th>
                        <th rowspan="2" style="width: 36px;">UMUR (TH)</th>
                        <th rowspan="2" style="width: 54px;">STATUS PERKAWINAN</th>
                        <th rowspan="2" style="width: 58px;">STATUS DALAM KELUARGA</th>
                        <th rowspan="2" style="width: 40px;">AGAMA</th>
                        <th rowspan="2" style="width: 64px;">ALAMAT</th>
                        <th rowspan="2" style="width: 56px;">DESA/KEL/SEJENIS</th>
                        <th rowspan="2" style="width: 50px;">PENDIDIKAN</th>
                        <th rowspan="2" style="width: 50px;">PEKERJAAN</th>
                        <th rowspan="2" style="width: 34px;">AKSEPTOR KB</th>
                        <th rowspan="2" style="width: 38px;">AKTIF POSYANDU</th>
                        <th rowspan="2" style="width: 34px;">IKUT BKB</th>
                        <th rowspan="2" style="width: 38px;">MEMILIKI TABUNGAN</th>
                        <th colspan="2" style="width: 72px;">KELOMPOK BELAJAR</th>
                        <th rowspan="2" style="width: 40px;">MENGIKUTI PAUD/SEJENIS</th>
                        <th rowspan="2" style="width: 40px;">IKUT KEGIATAN KOPERASI</th>
                    </tr>
                    <tr>
                        <th style="width: 16px;">L</th>
                        <th style="width: 16px;">P</th>
                        <th style="width: 26px;">IKUT</th>
                        <th style="width: 46px;">JENIS</th>
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

            <div class="meta">
                Wilayah: {{ $areaName }}<br>
                Dicetak oleh: {{ $printedBy?->name ?? '-' }}<br>
                Dicetak pada: {{ $printedAt->format('Y-m-d H:i:s') }}
            </div>
        </section>
    @endforeach
</body>
</html>
