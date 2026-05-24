<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Grafik Program - {{ $pokjaName }}</title>
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
    <h1>Laporan Grafik {{ $pokjaName }}</h1>
    
    <div class="meta">
        <strong>Wilayah:</strong> {{ $printedBy?->area?->name ?? '-' }} ({{ strtoupper((string) ($printedBy?->scope ?? '-')) }})<br>
        <strong>Tahun Anggaran:</strong> {{ $printedBy?->active_budget_year ?? '-' }}<br>
        <strong>Dicetak Oleh:</strong> {{ $printedBy?->name ?? '-' }} pada {{ now()->format('d/m/Y H:i') }}
    </div>

    @foreach($chartSvgs as $svg)
        <div class="chart-container">
            <img src="{{ $svg }}" class="chart-svg">
        </div>
    @endforeach

    <div style="margin-top: 20px; font-size: 9px; color: #666; text-align: center;">
        Dicetak dari Sistem Informasi PKK | Render: Vector SVG (Academic Style)
    </div>
</body>
</html>
