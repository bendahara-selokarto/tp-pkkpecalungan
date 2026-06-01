<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Buku Daftar Hadir</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #111827;
        }
        .title {
            font-size: 16px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 10px;
        }
        .meta {
            margin-bottom: 8px;
            font-size: 11px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #111827;
            padding: 4px;
            vertical-align: top;
            word-wrap: break-word;
        }
        th {
            background: #f3f4f6;
            text-align: center;
            font-size: 10px;
        }
        .center {
            text-align: center;
        }
        
    </style>
</head>
<body>
    @php
        $scopeLevel = \App\Domains\Wilayah\Enums\ScopeLevel::tryFrom((string) $level);
        $levelLabel = $scopeLevel?->reportLevelLabel() ?? strtoupper((string) $level);
        $areaLabel = $scopeLevel?->reportAreaLabel() ?? 'Wilayah';
        $budgetYearLabel = $tahunAnggaran ?? ($printedBy?->active_budget_year ?? now()->format('Y'));
    @endphp

    <div class="title">BUKU DAFTAR HADIR {{ $levelLabel }}</div>

    <div class="meta">
        {{ $areaLabel }}: {{ $areaName }}<br>
        Tahun Anggaran: {{ $budgetYearLabel }}<br>
        <br>
        Dicetak pada: {{ $printedAt->format('Y-m-d H:i:s') }}
    </div>

    <table>
        <colgroup>
            <col style="width: 35px;">
            <col style="width: 90px;">
            <col style="width: 210px;">
            <col style="width: 140px;">
            <col style="width: 140px;">
            <col style="width: 120px;">
        </colgroup>
        <thead>
            <tr>
                <th>NO</th>
                <th>TANGGAL</th>
                <th>KEGIATAN</th>
                <th>NAMA</th>
                <th>INSTANSI</th>
                <th>KETERANGAN</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $index => $item)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td class="center">{{ $item->attendance_date ? \Carbon\Carbon::parse((string) $item->attendance_date)->format('Y-m-d') : '-' }}</td>
                    <td>{{ $item->activity?->title ?: '-' }}</td>
                    <td>{{ $item->attendee_name }}</td>
                    <td>{{ $item->institution ?: '-' }}</td>
                    <td>{{ $item->description ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="center">Data belum tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @include('pdf.partials._report_footer')

    <div class="footer">
        Total data: {{ $items->count() }}. | 
        
    </div>
    @include('pdf.partials._report_metadata')
</body>
</html>
