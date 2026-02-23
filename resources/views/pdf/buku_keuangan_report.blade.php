<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Tabungan</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #111827; }
        .lampiran { text-align: right; font-size: 14px; font-weight: 700; margin-bottom: 16px; }
        .title { font-size: 16px; font-weight: 700; text-align: center; margin-bottom: 10px; }
        .meta { margin-bottom: 8px; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { border: 1px solid #111827; padding: 3px; vertical-align: top; word-break: break-word; }
        th { text-align: center; font-size: 8px; font-weight: 700; }
        .number-row th { font-size: 7px; font-weight: 400; }
        .center { text-align: center; }
        .right { text-align: right; }
        .summary { margin-top: 10px; font-size: 10px; line-height: 1.5; }
        .ttd { margin-top: 18px; width: 100%; border-collapse: collapse; }
        .ttd td { border: none; width: 50%; vertical-align: top; text-align: center; font-size: 10px; }
        .meta-print { margin-top: 8px; font-size: 9px; color: #374151; }
    </style>
</head>
<body>
    @php
        $scopeLevel = \App\Domains\Wilayah\Enums\ScopeLevel::tryFrom((string) $level);
        $levelLabel = $scopeLevel?->reportLevelLabel() ?? strtoupper((string) $level);
        $areaLabel = $scopeLevel?->reportAreaLabel() ?? 'Wilayah';
        $incomingEntries = collect($entries ?? [])->filter(fn (array $entry): bool => (float) ($entry['pemasukan'] ?? 0) > 0)->values();
        $outgoingEntries = collect($entries ?? [])->filter(fn (array $entry): bool => (float) ($entry['pengeluaran'] ?? 0) > 0)->values();
        $rowCount = max($incomingEntries->count(), $outgoingEntries->count(), 1);
        $totalIncoming = (float) $incomingEntries->sum(fn (array $entry): float => (float) ($entry['pemasukan'] ?? 0));
        $totalOutgoing = (float) $outgoingEntries->sum(fn (array $entry): float => (float) ($entry['pengeluaran'] ?? 0));
        $sisaKasTunai = $totalIncoming - $totalOutgoing;
        $sisaBank = 0.0;
        $totalSaldo = $sisaBank + $sisaKasTunai;
    @endphp

    <div class="lampiran">LAMPIRAN 4.11</div>
    <div class="title">BUKU TABUNGAN</div>
    <div class="meta">
        {{ $areaLabel }}: {{ $areaName }} | Level: {{ $levelLabel }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 3%;">NO</th>
                <th style="width: 8%;">TANGGAL,<br>BULAN,<br>TAHUN</th>
                <th style="width: 8%;">SUMBER DANA</th>
                <th style="width: 8%;">URAIAN</th>
                <th style="width: 8%;">NOMOR BUKTI KAS</th>
                <th style="width: 13%;">JUMLAH PENGELUARAN (Rp.)</th>
                <th style="width: 3%;">NO</th>
                <th style="width: 8%;">TANGGAL,<br>BULAN,<br>TAHUN</th>
                <th style="width: 8%;">SUMBER DANA</th>
                <th style="width: 8%;">URAIAN</th>
                <th style="width: 8%;">NOMOR BUKTI KAS</th>
                <th style="width: 13%;">JUMLAH PENERIMAAN (Rp.)</th>
            </tr>
            <tr class="number-row">
                <th>1</th>
                <th>2</th>
                <th>3</th>
                <th>4</th>
                <th>5</th>
                <th>6</th>
                <th>7</th>
                <th>8</th>
                <th>9</th>
                <th>10</th>
                <th>11</th>
                <th>12</th>
            </tr>
        </thead>
        <tbody>
            @for ($i = 0; $i < $rowCount; $i++)
                @php
                    $incoming = $incomingEntries->get($i);
                    $outgoing = $outgoingEntries->get($i);
                @endphp
                <tr>
                    <td class="center">{{ $incoming ? $i + 1 : '' }}</td>
                    <td class="center">
                        @if ($incoming)
                            {{ \Carbon\Carbon::parse($incoming['tanggal'])->format('d/m/Y') }}
                        @endif
                    </td>
                    <td class="center">{{ $incoming ? strtoupper(str_replace('_', ' ', (string) $incoming['sumber'])) : '' }}</td>
                    <td>{{ $incoming['uraian'] ?? '' }}</td>
                    <td class="center">{{ $incoming['nomor_bukti_kas'] ?? ($incoming ? sprintf('MASUK-%03d', $i + 1) : '') }}</td>
                    <td class="right">{{ $incoming ? number_format((float) ($incoming['pemasukan'] ?? 0), 0, ',', '.') : '' }}</td>
                    <td class="center">{{ $outgoing ? $i + 1 : '' }}</td>
                    <td class="center">
                        @if ($outgoing)
                            {{ \Carbon\Carbon::parse($outgoing['tanggal'])->format('d/m/Y') }}
                        @endif
                    </td>
                    <td class="center">{{ $outgoing ? strtoupper(str_replace('_', ' ', (string) $outgoing['sumber'])) : '' }}</td>
                    <td>{{ $outgoing['uraian'] ?? '' }}</td>
                    <td class="center">{{ $outgoing['nomor_bukti_kas'] ?? ($outgoing ? sprintf('KELUAR-%03d', $i + 1) : '') }}</td>
                    <td class="right">{{ $outgoing ? number_format((float) ($outgoing['pengeluaran'] ?? 0), 0, ',', '.') : '' }}</td>
                </tr>
            @endfor
            <tr>
                <td colspan="5" class="center"><strong>JUMLAH</strong></td>
                <td class="right"><strong>{{ number_format($totalIncoming, 0, ',', '.') }}</strong></td>
                <td colspan="5" class="center"><strong>JUMLAH</strong></td>
                <td class="right"><strong>{{ number_format($totalOutgoing, 0, ',', '.') }}</strong></td>
            </tr>
        </tbody>
    </table>

    <div class="summary">
        Pada hari ini ................................ tanggal ........ bulan ........ tahun ........ Buku Kas Umum ditutup dengan keadaan sebagai berikut:<br>
        Sisa Buku Kas Umum:<br>
        a. Sisa Bank: Rp. {{ number_format($sisaBank, 0, ',', '.') }}<br>
        b. Sisa Kas/Tunai: Rp. {{ number_format($sisaKasTunai, 0, ',', '.') }}<br>
        TOTAL: Rp. {{ number_format($totalSaldo, 0, ',', '.') }}
    </div>

    <table class="ttd">
        <tr>
            <td>
                Mengetahui,<br>
                Ketua Umum/Ketua
                <br><br><br>
                Tanda tangan
                <br><br>
                Nama Jelas
            </td>
            <td>
                Nama Kota, ..... tanggal ..... bulan ..... tahun<br>
                Bendahara
                <br><br><br>
                Tanda tangan
                <br><br>
                Nama Jelas
            </td>
        </tr>
    </table>

    <div class="meta-print">
        Dicetak oleh: {{ $printedBy?->name ?? '-' }} | Dicetak pada: {{ $printedAt->format('Y-m-d H:i:s') }}
    </div>
</body>
</html>
