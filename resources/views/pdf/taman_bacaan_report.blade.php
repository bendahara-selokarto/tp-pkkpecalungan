<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Taman Bacaan</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111827; }
        .sheet { page-break-after: always; }
        .sheet:last-child { page-break-after: auto; }
        .lampiran { text-align: right; font-size: 14px; font-weight: 700; margin-bottom: 14px; }
        .section { font-size: 14px; font-weight: 700; margin-bottom: 8px; }
        .identity { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        .identity td { border: none; padding: 2px 4px 2px 0; vertical-align: top; }
        .identity .label { width: 180px; font-weight: 700; }
        .identity .dot { width: 12px; text-align: center; font-weight: 700; }
        table.main { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .main th, .main td { border: 1px solid #111827; padding: 4px; vertical-align: top; word-break: break-word; }
        .main th { text-align: center; font-size: 10px; font-weight: 700; }
        .number-row th { font-size: 9px; font-weight: 400; }
        .center { text-align: center; }
        .left { text-align: left; }
        .note { margin-top: 8px; font-size: 10px; }
        .meta-print { margin-top: 8px; font-size: 9px; color: #374151; }
    </style>
</head>
<body>
    @php
        $scopeLevel = \App\Domains\Wilayah\Enums\ScopeLevel::tryFrom((string) $level);
        $desaKel = $scopeLevel === \App\Domains\Wilayah\Enums\ScopeLevel::DESA ? $areaName : '-';
        $kec = $scopeLevel === \App\Domains\Wilayah\Enums\ScopeLevel::KECAMATAN ? $areaName : '-';
        $groups = collect($items)
            ->groupBy(function ($item): string {
                return implode('|', [
                    (string) ($item->nama_taman_bacaan ?? ''),
                    (string) ($item->nama_pengelola ?? ''),
                    (string) ($item->jumlah_buku_bacaan ?? ''),
                ]);
            });

        if ($groups->isEmpty()) {
            $groups = collect(['__EMPTY__' => collect()]);
        }
    @endphp

    @foreach ($groups as $groupItems)
        @php
            $first = $groupItems->first();
            $totalJumlahJenis = $groupItems->sum(function ($item): int {
                return (int) (is_numeric($item->jumlah ?? null) ? $item->jumlah : 0);
            });
        @endphp
        <section class="sheet">
            <div class="lampiran">LAMPIRAN 4.14.4b</div>
            <div class="section">B. TAMAN BACAAN</div>

            <table class="identity">
                <tr>
                    <td class="label">DESA/KEL</td>
                    <td class="dot">:</td>
                    <td>{{ $desaKel }}</td>
                    <td class="label">Kec</td>
                    <td class="dot">:</td>
                    <td>{{ $kec }}</td>
                </tr>
                <tr>
                    <td class="label">KEB/KOTA</td>
                    <td class="dot">:</td>
                    <td>-</td>
                    <td class="label">Prov</td>
                    <td class="dot">:</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td class="label">Nama Taman Bacaan/ Perpustakaan</td>
                    <td class="dot">:</td>
                    <td colspan="4">{{ $first->nama_taman_bacaan ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Nama Pengelola</td>
                    <td class="dot">:</td>
                    <td colspan="4">{{ $first->nama_pengelola ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Jumlah Buku Bacaan</td>
                    <td class="dot">:</td>
                    <td colspan="4">{{ $first->jumlah_buku_bacaan ?? '-' }} Buku</td>
                </tr>
                <tr>
                    <td class="label">Jenis Buku Bacaan</td>
                    <td class="dot">:</td>
                    <td colspan="4">-</td>
                </tr>
            </table>

            <table class="main">
                <thead>
                    <tr>
                        <th style="width: 6%;">NO</th>
                        <th style="width: 54%;">JENIS BUKU</th>
                        <th style="width: 20%;">KATAGORI</th>
                        <th>JUMLAH</th>
                    </tr>
                    <tr class="number-row">
                        <th>1</th>
                        <th>2</th>
                        <th>3</th>
                        <th>4</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($groupItems as $index => $item)
                        <tr>
                            <td class="center">{{ $index + 1 }}</td>
                            <td>{{ $item->jenis_buku ?: '-' }}</td>
                            <td>{{ $item->kategori ?: '-' }}</td>
                            <td class="center">{{ $item->jumlah ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td class="center">1</td>
                            <td>-</td>
                            <td>-</td>
                            <td class="center">-</td>
                        </tr>
                    @endforelse
                    <tr>
                        <td colspan="3" class="center"><strong>JUMLAH</strong></td>
                        <td class="center"><strong>{{ $totalJumlahJenis > 0 ? $totalJumlahJenis : '-' }}</strong></td>
                    </tr>
                </tbody>
            </table>

            <div class="note">
                Jenis Buku : [Tanaman Hias, Tanaman Obat, Bacaan Anak, dll]<br>
                Katagori : [pertanian, pendidikan, ketrampilan keluarga]
            </div>

            <div class="meta-print">
                Dicetak oleh: {{ $printedBy?->name ?? '-' }} | Dicetak pada: {{ $printedAt->format('Y-m-d H:i:s') }}
            </div>
        </section>
    @endforeach
</body>
</html>


