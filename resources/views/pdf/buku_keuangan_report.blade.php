<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Keuangan</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111827; }
        .title { font-size: 16px; font-weight: 700; text-align: center; margin-bottom: 8px; }
        .meta { margin-bottom: 8px; font-size: 11px; }
        .note { margin-top: 8px; font-size: 10px; color: #374151; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { border: 1px solid #111827; padding: 4px; vertical-align: top; word-wrap: break-word; }
        th { background: #f3f4f6; text-align: center; font-size: 10px; }
        .center { text-align: center; }
        .right { text-align: right; }
    </style>
</head>
<body>
    @php
        $scopeLevel = \App\Domains\Wilayah\Enums\ScopeLevel::tryFrom((string) $level);
        $levelLabel = $scopeLevel?->reportLevelLabel() ?? strtoupper((string) $level);
        $areaLabel = $scopeLevel?->reportAreaLabel() ?? 'Wilayah';
        $saldo = 0;
    @endphp

    <div class="title">BUKU KEUANGAN {{ $levelLabel }}</div>
    <div class="meta">
        {{ $areaLabel }}: {{ $areaName }}<br>
        Dicetak oleh: {{ $printedBy?->name ?? '-' }}<br>
        Dicetak pada: {{ $printedAt->format('Y-m-d H:i:s') }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">NO</th>
                <th style="width: 84px;">TANGGAL</th>
                <th style="width: 170px;">URAIAN</th>
                <th style="width: 86px;">SUMBER</th>
                <th style="width: 92px;">PEMASUKAN</th>
                <th style="width: 92px;">PENGELUARAN</th>
                <th>SALDO</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($entries as $index => $entry)
                @php
                    $nominalMasuk = (float) $entry['pemasukan'];
                    $nominalKeluar = (float) $entry['pengeluaran'];
                    $saldo += $nominalMasuk - $nominalKeluar;
                @endphp
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td class="center">{{ \Carbon\Carbon::parse($entry['tanggal'])->format('d/m/Y') }}</td>
                    <td>{{ $entry['uraian'] }}</td>
                    <td class="center">{{ strtoupper(str_replace('_', ' ', (string) $entry['sumber'])) }}</td>
                    <td class="right">{{ $nominalMasuk > 0 ? number_format($nominalMasuk, 0, ',', '.') : '-' }}</td>
                    <td class="right">{{ $nominalKeluar > 0 ? number_format($nominalKeluar, 0, ',', '.') : '-' }}</td>
                    <td class="right">{{ number_format($saldo, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="center">Data keuangan belum tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="note">
        Sumber data diambil dari transaksi bantuan kategori keuangan. Kategori berisi kata
        "pengeluaran/keluar/belanja/debit" diperlakukan sebagai pengeluaran.
    </div>
</body>
</html>
