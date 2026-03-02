<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Naskah Pelaporan Pilot Project Pokja IV</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #111827;
            line-height: 1.4;
        }
        .title {
            font-size: 13px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 8px;
        }
        .meta {
            margin-bottom: 10px;
            font-size: 9px;
        }
        .section-title {
            margin-top: 8px;
            margin-bottom: 3px;
            font-size: 11px;
            font-weight: 700;
        }
        .block {
            padding: 2px 0;
            white-space: pre-line;
        }
        .list {
            margin: 0;
            padding-left: 16px;
        }
        .letterhead-wrap {
            margin-bottom: 8px;
        }
        .letterhead-top {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }
        .letterhead-top td {
            vertical-align: top;
        }
        .letterhead-logo {
            width: 82px;
        }
        .letterhead-logo img {
            width: 70px;
            height: auto;
        }
        .lampiran-label {
            text-align: right;
            font-weight: 700;
            font-size: 10px;
        }
        .letterhead-title {
            text-align: center;
            font-weight: 700;
            font-size: 11px;
            line-height: 1.35;
        }
        .letterhead-sub {
            text-align: center;
            font-size: 10px;
            line-height: 1.3;
            margin-top: 2px;
        }
        .letterhead-separator {
            border-top: 2px solid #111827;
            margin-top: 6px;
            margin-bottom: 6px;
        }
        .surat-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            font-size: 10px;
        }
        .surat-table td {
            border: 1px solid #111827;
            padding: 2px 4px;
            vertical-align: top;
        }
        .surat-table .label-col {
            width: 82px;
        }
        .surat-table .separator-col {
            width: 10px;
            text-align: center;
        }
        .page-break {
            page-break-after: always;
        }
        .attachment-list {
            margin-top: 6px;
            margin-bottom: 8px;
        }
        .attachment-item {
            margin-bottom: 8px;
        }
        .attachment-photo {
            height: 10cm;
            width: auto;
            max-width: 100%;
            border: 1px solid #cbd5e1;
            padding: 2px;
            display: block;
            margin-top: 4px;
        }
        .attachment-filename {
            font-size: 9px;
            margin-top: 3px;
        }
        .closing-text {
            margin-top: 14px;
        }
        .signature-wrap {
            width: 100%;
            margin-top: 18px;
            border-collapse: collapse;
        }
        .signature-wrap td {
            vertical-align: top;
        }
        .signature-right {
            width: 46%;
            text-align: center;
        }
        .signature-line {
            margin-top: 56px;
            letter-spacing: 1px;
            font-weight: 700;
        }
    </style>
</head>
<body>
@php
    $scopeLevel = \App\Domains\Wilayah\Enums\ScopeLevel::tryFrom((string) $level);
    $levelLabel = $scopeLevel?->reportLevelLabel() ?? strtoupper((string) $level);
    $areaLabel = $scopeLevel?->reportAreaLabel() ?? 'Wilayah';
    $items = collect($reports ?? []);
    $dotFill = static fn (int $length = 32): string => str_repeat('.', $length);
    $inlinePublicImageData = static function (string $relativePath): ?string {
        $absolutePath = public_path(ltrim($relativePath, '/'));
        if (! is_file($absolutePath)) {
            return null;
        }

        $binary = @file_get_contents($absolutePath);
        if ($binary === false) {
            return null;
        }

        $extension = strtolower((string) pathinfo($absolutePath, PATHINFO_EXTENSION));
        $mime = match ($extension) {
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'webp' => 'image/webp',
            default => 'application/octet-stream',
        };

        return 'data:' . $mime . ';base64,' . base64_encode($binary);
    };
    $canRenderPng = function_exists('imagecreatefrompng');
    $logoDataUri = $canRenderPng ? $inlinePublicImageData('images/pkk-logo.png') : null;
    $scopeInstitution = (string) match ((string) $level) {
        'desa' => 'DESA',
        'kecamatan' => 'KECAMATAN',
        default => strtoupper((string) $level),
    };
    $scopeInstitutionTitle = (string) match ((string) $level) {
        'desa' => 'Desa',
        'kecamatan' => 'Kecamatan',
        default => ucfirst((string) $level),
    };
    $inlineImageData = static function (string $relativePath, ?string $mimeType = null): ?string {
        $path = ltrim($relativePath, '/');
        $disk = \Illuminate\Support\Facades\Storage::disk('public');
        if (! $disk->exists($path)) {
            return null;
        }

        $extension = strtolower((string) pathinfo($path, PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        if (! in_array($extension, $allowedExtensions, true)) {
            return null;
        }

        if ($extension === 'png' && ! function_exists('imagecreatefrompng')) {
            return null;
        }

        // Dompdf is often inconsistent rendering webp. Convert to png when possible.
        if ($extension === 'webp' && function_exists('imagecreatefromwebp') && function_exists('imagepng')) {
            try {
                $absolutePath = $disk->path($path);
                $imageResource = @imagecreatefromwebp($absolutePath);
                if ($imageResource !== false) {
                    ob_start();
                    imagepng($imageResource);
                    $pngBinary = ob_get_clean();
                    imagedestroy($imageResource);

                    if (is_string($pngBinary) && $pngBinary !== '') {
                        return 'data:image/png;base64,' . base64_encode($pngBinary);
                    }
                }
            } catch (\Throwable $throwable) {
                // continue with default reader path
            }
        }

        try {
            $binary = $disk->get($path);
        } catch (\Throwable $throwable) {
            $binary = false;
        }
        if ($binary === false || $binary === '') {
            return null;
        }

        $resolvedMimeType = strtolower(trim((string) ($mimeType ?? '')));
        if (! str_starts_with($resolvedMimeType, 'image/')) {
            try {
                $resolvedMimeType = strtolower((string) $disk->mimeType($path));
            } catch (\Throwable $throwable) {
                $resolvedMimeType = '';
            }
        }
        if (! str_starts_with($resolvedMimeType, 'image/')) {
            $resolvedMimeType = match ($extension) {
                'png' => 'image/png',
                'jpg', 'jpeg' => 'image/jpeg',
                'webp' => 'image/webp',
                default => 'application/octet-stream',
            };
        }

        if ($resolvedMimeType === 'image/png' && ! function_exists('imagecreatefrompng')) {
            return null;
        }

        return 'data:' . $resolvedMimeType . ';base64,' . base64_encode($binary);
    };
@endphp

@forelse ($items as $index => $report)
    @php
        $groupedAttachments = collect(data_get($report, 'attachments', []))->groupBy('category');
        $defaultHal = 'Penyampaian Laporan Pelaksanaan Pilot Project Gerakan Keluarga Sehat Tanggap dan Tangguh Bencana';
        $fromAutoValue = 'Tim Penggerak PKK ' . $scopeInstitutionTitle . ' ' . $areaName;
        $dotOrText = static fn (?string $text, int $dotLength = 74): string => trim((string) $text) !== ''
            ? trim((string) $text)
            : $dotFill($dotLength);
        $fromInput = trim((string) data_get($report, 'surat_dari', ''));
        $fromValue = $fromInput !== '' ? $fromInput : $fromAutoValue;

        $halValue = trim((string) data_get($report, 'surat_hal', ''));
        if ($halValue === '') {
            $halValue = trim((string) data_get($report, 'judul_laporan', ''));
        }
        if ($halValue === '') {
            $halValue = $defaultHal;
        }

        $tanggalValue = $printedAt->translatedFormat('d F Y');
        $tanggalRaw = data_get($report, 'surat_tanggal');
        if (filled($tanggalRaw)) {
            try {
                $tanggalValue = \Illuminate\Support\Carbon::parse((string) $tanggalRaw)->translatedFormat('d F Y');
            } catch (\Throwable $throwable) {
                $tanggalValue = $printedAt->translatedFormat('d F Y');
            }
        }
    @endphp

    <div class="letterhead-wrap">
        <table class="letterhead-top">
            <tr>
                <td colspan="2" class="lampiran-label">LAMPIRAN 2</td>
            </tr>
            <tr>
                <td class="letterhead-logo">
                    @if ($logoDataUri !== null)
                        <img src="{{ $logoDataUri }}" alt="Logo TP PKK">
                    @endif
                </td>
                <td>
                    <div class="letterhead-title">
                        PEMBERDAYAAN DAN KESEJAHTERAAN KELUARGA<br>
                        TP PKK {{ $scopeInstitution }} {{ strtoupper($areaName) }}
                    </div>
                    <div class="letterhead-sub">
                        d/a {{ $areaName }} {{ $dotFill(54) }}<br>
                        Telp. {{ $dotFill(18) }} email: {{ $dotFill(24) }}<br>
                        Website: http://{{ $dotFill(16) }}
                    </div>
                </td>
            </tr>
        </table>
        <div class="letterhead-separator"></div>

        <table class="surat-table">
            <tr>
                <td class="label-col">Kepada</td>
                <td class="separator-col">:</td>
                <td>{{ $dotOrText((string) data_get($report, 'surat_kepada', '')) }}</td>
            </tr>
            <tr>
                <td class="label-col">Dari</td>
                <td class="separator-col">:</td>
                <td>{{ $fromValue }}</td>
            </tr>
            <tr>
                <td class="label-col">Tembusan</td>
                <td class="separator-col">:</td>
                <td>{{ $dotOrText((string) data_get($report, 'surat_tembusan', '')) }}</td>
            </tr>
            <tr>
                <td class="label-col">Tanggal</td>
                <td class="separator-col">:</td>
                <td>{{ $tanggalValue }}</td>
            </tr>
            <tr>
                <td class="label-col">Nomor</td>
                <td class="separator-col">:</td>
                <td>{{ $dotOrText((string) data_get($report, 'surat_nomor', '')) }}</td>
            </tr>
            <tr>
                <td class="label-col">Sifat</td>
                <td class="separator-col">:</td>
                <td>{{ $dotOrText((string) data_get($report, 'surat_sifat', '')) }}</td>
            </tr>
            <tr>
                <td class="label-col">Lampiran</td>
                <td class="separator-col">:</td>
                <td>{{ $dotOrText((string) data_get($report, 'surat_lampiran', '')) }}</td>
            </tr>
            <tr>
                <td class="label-col">Hal</td>
                <td class="separator-col">:</td>
                <td>{{ $halValue }}</td>
            </tr>
        </table>
    </div>

    <div class="section-title">I. Dasar Pelaksanaan</div>
    <div class="block">{{ data_get($report, 'dasar_pelaksanaan', '-') }}</div>

    <div class="section-title">II. Pendahuluan</div>
    <div class="block">{{ data_get($report, 'pendahuluan', '-') }}</div>

    <div class="section-title">IV. Pelaksanaan</div>
    <ol class="list">
        <li>{{ data_get($report, 'pelaksanaan_1', '-') }}</li>
        <li>{{ data_get($report, 'pelaksanaan_2', '-') }}</li>
        <li>{{ data_get($report, 'pelaksanaan_3', '-') }}</li>
        <li>{{ data_get($report, 'pelaksanaan_4', '-') }}</li>
        <li>{{ data_get($report, 'pelaksanaan_5', '-') }}</li>
        <li>Lampiran dokumentasi (a-e) sesuai ketentuan.</li>
    </ol>

    @php
        $attachmentNames = static fn (string $category): string => collect($groupedAttachments->get($category, collect()))
            ->pluck('original_name')
            ->filter(fn ($value) => trim((string) $value) !== '')
            ->implode(', ');
    @endphp

    <div class="section-title">VI. Lampiran Dokumentasi</div>
    <ol class="list" style="list-style-type: lower-alpha;">
        <li>
            {{ data_get($categoryLabels, '6a_photo', '6.a Foto dokumentasi kegiatan pemantauan dan pembinaan') }}
            <div class="attachment-filename">{{ $attachmentNames('6a_photo') !== '' ? $attachmentNames('6a_photo') : '-' }}</div>
        </li>
        <li>
            {{ data_get($categoryLabels, '6b_photo', '6.b Foto kegiatan inovasi') }}
            <div class="attachment-filename">{{ $attachmentNames('6b_photo') !== '' ? $attachmentNames('6b_photo') : '-' }}</div>
        </li>
        <li>
            6.c Laporan manual pilot project (diisi pada submenu laporan manual yang sudah tersedia)
        </li>
        <li>
            {{ data_get($categoryLabels, '6d_document', '6.d Dokumen pembekalan/pelatihan kader') }}
            <div class="attachment-filename">{{ $attachmentNames('6d_document') !== '' ? $attachmentNames('6d_document') : '-' }}</div>
        </li>
        <li>
            {{ data_get($categoryLabels, '6e_photo', '6.e Foto dokumentasi kondisi dan keadaan lokasi') }}
            <div class="attachment-filename">{{ $attachmentNames('6e_photo') !== '' ? $attachmentNames('6e_photo') : '-' }}</div>
        </li>
    </ol>

    <div class="section-title">V. Penutup</div>
    <div class="block">{{ data_get($report, 'penutup', '-') }}</div>

    <div class="closing-text">Demikian kami sampaikan laporan ...</div>

    <table class="signature-wrap">
        <tr>
            <td></td>
            <td class="signature-right">
                Ketua Bidang IV / Pokja IV<br>
                Tim Penggerak PKK {{ $scopeInstitutionTitle }} {{ $areaName }},
                <div class="signature-line">{{ $dotFill(42) }}</div>
            </td>
        </tr>
    </table>

    @php
        $visualAttachments = collect(data_get($report, 'attachments', []))
            ->filter(function ($attachment): bool {
                $path = strtolower((string) data_get($attachment, 'file_path', ''));
                $extension = pathinfo($path, PATHINFO_EXTENSION);

                return in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true);
            })
            ->values();
    @endphp

    @if ($visualAttachments->isNotEmpty())
        <div class="page-break"></div>
        <div class="section-title">Lampiran Visual</div>
        <ol class="list">
            @foreach ($visualAttachments as $attachment)
                @php
                    $mime = (string) data_get($attachment, 'mime_type', '');
                    $fileName = (string) data_get($attachment, 'original_name', '-');
                    $imageData = $inlineImageData((string) data_get($attachment, 'file_path', ''), $mime);
                    $category = (string) data_get($attachment, 'category', '');
                    $categoryText = data_get($categoryLabels, $category, $category);
                @endphp
                <li class="attachment-item">
                    <div><strong>{{ $categoryText }}</strong></div>
                    <div class="attachment-filename">{{ $fileName }}</div>
                    @if ($imageData !== null)
                        <img src="{{ $imageData }}" alt="{{ $fileName }}" class="attachment-photo">
                    @else
                        <div class="attachment-filename">[gambar tidak dapat dirender]</div>
                    @endif
                </li>
            @endforeach
        </ol>
    @endif

    @if ($index !== $items->count() - 1)
        <div class="page-break"></div>
    @endif
@empty
    <div class="title">NASKAH PELAPORAN PILOT PROJECT {{ $levelLabel }}</div>
    <div class="meta">
        {{ $areaLabel }}: {{ $areaName }}<br>
        Dicetak oleh: {{ $printedBy?->name ?? '-' }}<br>
        Dicetak pada: {{ $printedAt->format('Y-m-d H:i:s') }}
    </div>
    <p>Data naskah belum tersedia.</p>
@endforelse
</body>
</html>
