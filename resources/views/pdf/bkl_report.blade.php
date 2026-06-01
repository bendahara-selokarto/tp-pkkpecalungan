<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>REKAPITULASI DATA KELOMPOK BKL</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111827; }
        .title { font-size: 16px; font-weight: 700; text-align: center; margin-bottom: 8px; }
        .meta { margin-bottom: 8px; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #111827; padding: 4px; vertical-align: top; word-wrap: break-word; }
        th { background: #f3f4f6; text-align: center; font-size: 10px; }
        .center { text-align: center; }
        .signature { margin-top: 20px; border-collapse: collapse; width: 100%; }
        .signature td { border: none; padding: 0; vertical-align: top; font-size: 12px; }
        .signature .placeholder { margin-top: 52px; }
        .note { margin-top: 14px; font-size: 11px; }
    </style>
</head>
<body>
    <div class="title">REKAPITULASI DATA KELOMPOK BKL</div>
    <div class="meta">
        KEC {{ $pdfKecamatanName ?? $areaName }}<br>
        Tahun Anggaran: {{ $budgetYearLabel ?? '-' }}
    </div>

    <table>
        <colgroup>
            <col style="width: 35px;">
            <col style="width: 120px;">
            <col style="width: 150px;">
            <col style="width: 150px;">
            <col style="width: 160px;">
            <col style="width: 80px;">
            <col style="width: 150px;">
        </colgroup>
        <thead>
            <tr>
                <th>NO</th>
                <th>DESA</th>
                <th>NAMA BKL</th>
                <th>NO/TGL SK</th>
                <th>NAMA KETUA KELOMPOK</th>
                <th>JUMLAH ANGGOTA</th>
                <th>KEGIATAN</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $index => $item)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $item->desa }}</td>
                    <td>{{ $item->nama_bkl }}</td>
                    <td>{{ $item->no_tgl_sk }}</td>
                    <td>{{ $item->nama_ketua_kelompok }}</td>
                    <td class="center">{{ $item->jumlah_anggota }}</td>
                    <td>{{ $item->kegiatan }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="center">Data belum tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @include('pdf.partials._report_footer')

    <div class="note" style="margin-top: 8px; font-size: 9px; color: #374151;">
        Keterangan : Diisi oleh TP. PKK Kecamatan | 
        Dicetak oleh: {{ $printedBy?->name ?? '-' }} | Dicetak pada: {{ $printedAt->format('Y-m-d H:i:s') }}
    </div>
</body>
</html>
