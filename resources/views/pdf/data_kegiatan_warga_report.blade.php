<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kegiatan Warga</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111827; }
        .lampiran { text-align: right; font-size: 14px; font-weight: 700; margin-bottom: 16px; }
        .title { font-size: 16px; font-weight: 700; text-align: center; margin-bottom: 8px; }
        .meta { margin-bottom: 8px; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #111827; padding: 4px; vertical-align: top; word-break: break-word; }
        th { text-align: center; font-size: 9px; font-weight: 700; background-color: #f9fafb; }
        .center { text-align: center; }
        .bg-gray { background-color: #f3f4f6; }
    </style>
</head>
<body>
    @php
        $scopeLevel = \App\Domains\Wilayah\Enums\ScopeLevel::tryFrom((string) $level);
        $isKecamatan = $level === \App\Domains\Wilayah\Enums\ScopeLevel::KECAMATAN->value;
        $levelLabel = $scopeLevel?->reportLevelLabel() ?? strtoupper((string) $level);
        $areaLabel = $scopeLevel?->reportAreaLabel() ?? 'Wilayah';
        $kegiatanOptions = \App\Domains\Wilayah\DataKegiatanWarga\Models\DataKegiatanWarga::kegiatanOptions();
    @endphp

    <div class="lampiran">LAMPIRAN 4.14.1b</div>
    <div class="title">KEGIATAN WARGA</div>
    <div class="meta">
        {{ $areaLabel }}: {{ $areaName }} | Level: {{ $levelLabel }} | Tahun Anggaran: {{ $budgetYearLabel ?? '-' }}
    </div>

    @if($isKecamatan)
        {{-- Mode Rekap Kecamatan --}}
        <table>
            <thead>
                <tr>
                    <th rowspan="2" style="width: 30px;">NO</th>
                    <th rowspan="2" style="width: 120px;">NAMA DESA</th>
                    @foreach($kegiatanOptions as $kegiatan)
                        <th colspan="2">{{ strtoupper($kegiatan) }}</th>
                    @endforeach
                </tr>
                <tr>
                    @foreach($kegiatanOptions as $kegiatan)
                        <th style="width: 25px;">AKT</th>
                        <th>KET</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($items as $index => $item)
                    <tr>
                        <td class="center">{{ $index + 1 }}</td>
                        <td>{{ $item['nama_desa'] }}</td>
                        @foreach($item['activities'] as $act)
                            <td class="center">{{ $act['aktivitas'] ? 'Y' : 'T' }}</td>
                            <td style="font-size: 8px;">{{ $act['keterangan'] }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        {{-- Mode Detail Desa --}}
        @php
            $mappedItems = collect($items)->groupBy(function ($item): string {
                return strtolower(trim((string) $item->kegiatan));
            });
        @endphp
        <table>
            <colgroup>
                <col style="width: 35px;">
                <col style="width: 260px;">
                <col style="width: 90px;">
                <col style="width: 250px;">
            </colgroup>
            <thead>
                <tr>
                    <th>NO</th>
                    <th>KEGIATAN</th>
                    <th>AKTIVITAS (Y/T)</th>
                    <th>KETERANGAN (JENIS KEGIATAN YANG DIIKUTI)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($kegiatanOptions as $index => $kegiatanName)
                    @php
                        $rows = $mappedItems->get(strtolower(trim($kegiatanName)), collect());
                        $hasData = $rows->isNotEmpty();
                        $aktif = $hasData ? ($rows->contains(fn ($row): bool => (bool) $row->aktivitas) ? 'Y' : 'T') : 'T';
                        $keterangan = $hasData
                            ? $rows->pluck('keterangan')->filter(fn ($value): bool => filled($value))->implode('; ')
                            : '-';
                    @endphp
                    <tr>
                        <td class="center">{{ $index + 1 }}</td>
                        <td>{{ $kegiatanName }}</td>
                        <td class="center">{{ $aktif }}</td>
                        <td>{{ $keterangan !== '' ? $keterangan : '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @include('pdf.partials._report_footer')

    @include('pdf.partials._report_metadata')
</body>
</html>
