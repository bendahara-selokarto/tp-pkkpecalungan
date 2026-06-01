<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Isian Koperasi</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111827; }
        .title { font-size: 16px; font-weight: 700; text-align: center; margin-bottom: 8px; }
        .meta { margin-bottom: 8px; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #111827; padding: 4px; vertical-align: top; word-wrap: break-word; }
        th { background: #f3f4f6; text-align: center; font-size: 10px; }
        .center { text-align: center; }
        
    </style>
</head>
<body>
    @php
        $scopeLevel = \App\Domains\Wilayah\Enums\ScopeLevel::tryFrom((string) $level);
        $levelLabel = $scopeLevel?->reportLevelLabel() ?? strtoupper((string) $level);
        
        $headerVillage = ($scopeLevel === \App\Domains\Wilayah\Enums\ScopeLevel::DESA) ? $areaName : null;
        $budgetYearLabel = $tahunAnggaran ?? ($printedBy?->active_budget_year ?? now()->format('Y'));
    @endphp

    @include('pdf.partials._report_header', [
        'headerTitle' => 'DATA ISIAN KOPERASI',
        'headerRole' => $levelLabel,
        'headerLampiran' => 'LAMPIRAN 4.14.4c',
        'headerVillage' => $headerVillage,
        'headerKecamatan' => $pdfKecamatanName ?? null,
        'headerYear' => $budgetYearLabel
    ])

    <table>
        <colgroup>
            <col style="width: 35px;">
            <col style="width: 200px;">
            <col style="width: 180px;">
            <col style="width: 80px;">
            <col style="width: 80px;">
            <col style="width: 80px;">
            <col style="width: 80px;">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="2">NO</th>
                <th rowspan="2">NAMA KOPERASI</th>
                <th rowspan="2">JENIS USAHA</th>
                <th colspan="2">STATUS HUKUM</th>
                <th colspan="2">JUMLAH ANGGOTA</th>
            </tr>
            <tr>
                <th>BERBADAN HUKUM</th>
                <th>BLM. BERBADAN HUKUM</th>
                <th>L</th>
                <th>P</th>
            </tr>
            <tr class="number-row">
                @for ($i = 1; $i <= 7; $i++)
                    <th>{{ $i }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $index => $item)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $item->nama_koperasi }}</td>
                    <td>{{ $item->jenis_usaha }}</td>
                    <td class="center">{{ $item->berbadan_hukum ? 'Ya' : '-' }}</td>
                    <td class="center">{{ $item->belum_berbadan_hukum ? 'Ya' : '-' }}</td>
                    <td class="center">{{ $item->jumlah_anggota_l }}</td>
                    <td class="center">{{ $item->jumlah_anggota_p }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="center">Data belum tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @include('pdf.partials._report_footer')

    
    @include('pdf.partials._report_metadata')
</body>
</html>
