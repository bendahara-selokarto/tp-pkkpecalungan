<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Kegiatan #{{ $activity->id }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #111827;
            line-height: 1.5;
        }
        .title {
            font-size: 16px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 8px;
        }
        .meta {
            margin-bottom: 8px;
            font-size: 11px;
        }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { border: 1px solid #111827; padding: 4px; vertical-align: top; word-wrap: break-word; }
        th { background: #f3f4f6; text-align: center; font-size: 10px; }
        .center { text-align: center; }
        .footer {
            margin-top: 12px;
            font-size: 11px;
            color: #374151;
        }
    </style>
</head>
<body>
    <div class="title">BUKU KEGIATAN TP PKK</div>
    <div class="meta">
        Wilayah: {{ $activity->area?->name ?? '-' }}<br>
        Level: {{ strtoupper($activity->level) }}<br>
        Dibuat oleh sistem: {{ $activity->creator?->name ?? '-' }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 28px;">NO</th>
                <th style="width: 110px;">NAMA</th>
                <th style="width: 100px;">JABATAN</th>
                <th style="width: 80px;">TANGGAL</th>
                <th style="width: 100px;">TEMPAT</th>
                <th>URAIAN</th>
                <th style="width: 90px;">TANDA TANGAN</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="center">1</td>
                <td>{{ $activity->nama_petugas ?: $activity->title }}</td>
                <td>{{ $activity->jabatan_petugas ?: '-' }}</td>
                <td class="center">{{ \Carbon\Carbon::parse($activity->activity_date)->format('d/m/Y') }}</td>
                <td>{{ $activity->tempat_kegiatan ?: ($activity->area?->name ?? '-') }}</td>
                <td>{{ $activity->uraian ?: ($activity->description ?: '-') }}</td>
                <td>{{ $activity->tanda_tangan ?: ($activity->nama_petugas ?: ($activity->creator?->name ?? '-')) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Dicetak oleh {{ $printedBy?->name ?? '-' }} pada {{ $printedAt->format('Y-m-d H:i:s') }}.
    </div>
</body>
</html>
