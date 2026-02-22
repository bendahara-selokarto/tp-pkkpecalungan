<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Isian Posyandu oleh TP PKK</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111827; }
        .title { font-size: 16px; font-weight: 700; text-align: center; margin-bottom: 8px; }
        .meta { margin-bottom: 8px; font-size: 11px; line-height: 1.45; }
        .identity { width: 100%; margin: 8px 0; border-collapse: collapse; }
        .identity td { border: none; padding: 1px 0; vertical-align: top; }
        .identity .label { width: 105px; font-weight: 700; }
        .identity .label-right { width: 52px; font-weight: 700; }
        .identity .colon { width: 10px; text-align: center; }
        .identity .value { width: 250px; }
        .report-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { border: 1px solid #111827; padding: 4px; vertical-align: top; word-wrap: break-word; }
        th { background: #f3f4f6; text-align: center; font-size: 10px; }
        .center { text-align: center; }
        .section { margin-bottom: 14px; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    @php
        $scopeLevel = \App\Domains\Wilayah\Enums\ScopeLevel::tryFrom((string) $level);
        $levelLabel = $scopeLevel?->reportLevelLabel() ?? strtoupper((string) $level);
        $resolvedArea = $area ?? null;

        $desaKelName = '-';
        $kecamatanName = '-';

        if ($scopeLevel?->value === \App\Domains\Wilayah\Enums\ScopeLevel::DESA->value) {
            $desaKelName = $resolvedArea?->name ?? $areaName;
            $kecamatanName = $resolvedArea?->parent?->name ?? '-';
        } elseif ($scopeLevel?->value === \App\Domains\Wilayah\Enums\ScopeLevel::KECAMATAN->value) {
            $kecamatanName = $resolvedArea?->name ?? $areaName;
        }

        $groupedItems = $items->groupBy(static fn ($item) => implode('|', [
            strtolower(trim((string) $item->nama_posyandu)),
            strtolower(trim((string) $item->nama_pengelola)),
            strtolower(trim((string) $item->nama_sekretaris)),
            strtolower(trim((string) $item->jenis_posyandu)),
            (string) $item->jumlah_kader,
        ]))->values();
    @endphp

    <div class="title">DATA ISIAN POSYANDU OLEH TP PKK {{ $levelLabel }}</div>
    <div class="meta">
        Dicetak oleh: {{ $printedBy?->name ?? '-' }}<br>
        Dicetak pada: {{ $printedAt->format('Y-m-d H:i:s') }}
    </div>

    @if ($groupedItems->isEmpty())
        <div class="section">
            <table class="identity">
                <tr>
                    <td class="label">DESA/KEL</td><td class="colon">:</td><td class="value">{{ $desaKelName }}</td>
                    <td class="label-right">Kec</td><td class="colon">:</td><td class="value">{{ $kecamatanName }}</td>
                </tr>
                <tr>
                    <td class="label">KEB/KOTA</td><td class="colon">:</td><td class="value">-</td>
                    <td class="label-right">Prov</td><td class="colon">:</td><td class="value">-</td>
                </tr>
                <tr><td colspan="6">&nbsp;</td></tr>
                <tr><td class="label">Nama Posyandu</td><td class="colon">:</td><td class="value">-</td><td colspan="3"></td></tr>
                <tr><td class="label">Pengelola</td><td class="colon">:</td><td class="value">-</td><td colspan="3"></td></tr>
                <tr><td class="label">Sekretaris</td><td class="colon">:</td><td class="value">-</td><td colspan="3"></td></tr>
                <tr><td class="label">Jenis Posyandu</td><td class="colon">:</td><td class="value">-</td><td colspan="3"></td></tr>
                <tr><td class="label">Jumlah Kader</td><td class="colon">:</td><td class="value">-</td><td colspan="3"></td></tr>
            </table>

            <table class="report-table">
                <thead>
                    <tr>
                        <th style="width: 36px;" rowspan="3">NO</th>
                        <th style="width: 205px;" rowspan="3">JENIS KEGIATAN/LAYANAN</th>
                        <th style="width: 132px;" rowspan="3">FREKUENSI LAYANAN</th>
                        <th colspan="4">JUMLAH</th>
                        <th style="width: 132px;" rowspan="3">KETERANGAN</th>
                    </tr>
                    <tr>
                        <th colspan="2">PENGUNJUNG</th>
                        <th colspan="2">PETUGAS/PARAMEDIS</th>
                    </tr>
                    <tr>
                        <th style="width: 54px;">L</th>
                        <th style="width: 54px;">P</th>
                        <th style="width: 54px;">L</th>
                        <th style="width: 54px;">P</th>
                    </tr>
                    <tr>
                        <th>1</th>
                        <th>2</th>
                        <th>3</th>
                        <th>4</th>
                        <th>5</th>
                        <th>6</th>
                        <th>7</th>
                        <th>8</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="center">1</td>
                        <td>-</td>
                        <td class="center">-</td>
                        <td class="center">0</td>
                        <td class="center">0</td>
                        <td class="center">0</td>
                        <td class="center">0</td>
                        <td>-</td>
                    </tr>
                </tbody>
            </table>
        </div>
    @else
        @foreach ($groupedItems as $group)
            @php
                $first = $group->first();
            @endphp

            <div class="section">
                <table class="identity">
                    <tr>
                        <td class="label">DESA/KEL</td><td class="colon">:</td><td class="value">{{ $desaKelName }}</td>
                        <td class="label-right">Kec</td><td class="colon">:</td><td class="value">{{ $kecamatanName }}</td>
                    </tr>
                    <tr>
                        <td class="label">KEB/KOTA</td><td class="colon">:</td><td class="value">-</td>
                        <td class="label-right">Prov</td><td class="colon">:</td><td class="value">-</td>
                    </tr>
                    <tr><td colspan="6">&nbsp;</td></tr>
                    <tr><td class="label">Nama Posyandu</td><td class="colon">:</td><td class="value">{{ $first->nama_posyandu }}</td><td colspan="3"></td></tr>
                    <tr><td class="label">Pengelola</td><td class="colon">:</td><td class="value">{{ $first->nama_pengelola }}</td><td colspan="3"></td></tr>
                    <tr><td class="label">Sekretaris</td><td class="colon">:</td><td class="value">{{ $first->nama_sekretaris }}</td><td colspan="3"></td></tr>
                    <tr><td class="label">Jenis Posyandu</td><td class="colon">:</td><td class="value">{{ $first->jenis_posyandu }}</td><td colspan="3"></td></tr>
                    <tr><td class="label">Jumlah Kader</td><td class="colon">:</td><td class="value">{{ $first->jumlah_kader }}</td><td colspan="3"></td></tr>
                </table>

                <table class="report-table">
                    <thead>
                        <tr>
                            <th style="width: 36px;" rowspan="3">NO</th>
                            <th style="width: 205px;" rowspan="3">JENIS KEGIATAN/LAYANAN</th>
                            <th style="width: 132px;" rowspan="3">FREKUENSI LAYANAN</th>
                            <th colspan="4">JUMLAH</th>
                            <th style="width: 132px;" rowspan="3">KETERANGAN</th>
                        </tr>
                        <tr>
                            <th colspan="2">PENGUNJUNG</th>
                            <th colspan="2">PETUGAS/PARAMEDIS</th>
                        </tr>
                        <tr>
                            <th style="width: 54px;">L</th>
                            <th style="width: 54px;">P</th>
                            <th style="width: 54px;">L</th>
                            <th style="width: 54px;">P</th>
                        </tr>
                        <tr>
                            <th>1</th>
                            <th>2</th>
                            <th>3</th>
                            <th>4</th>
                            <th>5</th>
                            <th>6</th>
                            <th>7</th>
                            <th>8</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($group as $index => $item)
                            <tr>
                                <td class="center">{{ $index + 1 }}</td>
                                <td>{{ $item->jenis_kegiatan }}</td>
                                <td class="center">{{ $item->frekuensi_layanan }}</td>
                                <td class="center">{{ $item->jumlah_pengunjung_l }}</td>
                                <td class="center">{{ $item->jumlah_pengunjung_p }}</td>
                                <td class="center">{{ $item->jumlah_petugas_l }}</td>
                                <td class="center">{{ $item->jumlah_petugas_p }}</td>
                                <td>{{ $item->keterangan ?: '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if (! $loop->last)
                <div class="page-break"></div>
            @endif
        @endforeach
    @endif
</body>
</html>





