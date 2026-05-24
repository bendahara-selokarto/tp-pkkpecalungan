<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Grafik - Laporan Data Umum PKK</title>
    <style>
        @page { margin: 30px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #000; margin: 0; padding: 0; }
        h1 { margin: 0 0 10px; font-size: 18px; text-transform: uppercase; border-bottom: 3px solid #000; padding-bottom: 5px; text-align: center; }
        .meta { margin-bottom: 30px; line-height: 1.6; font-size: 12px; border: 1px solid #000; padding: 10px; background: #f9f9f9; }
        
        .chart-container { 
            margin-bottom: 40px; 
            page-break-inside: avoid; 
            text-align: center;
        }
        
        .chart-svg {
            width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
    @php
        $budgetYearLabel = (string) ($filters['tahun_anggaran'] ?? ($printedBy?->active_budget_year ?? '-'));
    @endphp

    <h1>Buku Grafik PKK</h1>
    
    <div class="meta">
        <strong>Wilayah:</strong> {{ $printedBy?->area?->name ?? '-' }} ({{ strtoupper((string) ($printedBy?->scope ?? '-')) }})<br>
        <strong>Tahun Anggaran:</strong> {{ $budgetYearLabel }}<br>
        <strong>Dicetak Oleh:</strong> {{ $printedBy?->name ?? '-' }} pada {{ $printedAt->format('d/m/Y H:i') }}
    </div>

    @foreach($chartSvgs as $svg)
        <div class="chart-container">
            <img src="{{ $svg }}" class="chart-svg">
        </div>
    @endforeach

</body>
</html>
