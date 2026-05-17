<?php

namespace App\Support\Pdf;

use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DompdfPdf;
use InvalidArgumentException;

class PdfViewFactory
{
    public const PAPER_SIZE_A4 = 'a4';

    public const PAPER_SIZE_F4 = [0, 0, 609.45, 935.43];

    public const ORIENTATION_LANDSCAPE = 'landscape';

    public const ORIENTATION_PORTRAIT = 'portrait';

    public function loadView(
        string $view,
        array $data = [],
        ?string $orientation = null,
        string|array|null $paperSize = null
    ): DompdfPdf {
        $resolvedOrientation = $orientation ?? self::ORIENTATION_LANDSCAPE;
        $resolvedPaperSize = $paperSize ?? config('pdf.default_paper', self::PAPER_SIZE_F4);

        if (! in_array($resolvedOrientation, [self::ORIENTATION_LANDSCAPE, self::ORIENTATION_PORTRAIT], true)) {
            throw new InvalidArgumentException('Invalid PDF orientation. Use landscape or portrait.');
        }

        $data = $this->appendStandardMetadata($data);

        return Pdf::loadView($view, $data)->setPaper($resolvedPaperSize, $resolvedOrientation);
    }

    private function appendStandardMetadata(array $data): array
    {
        $user = auth()->user();
        if (! $user) {
            return $data;
        }

        $role = $user->roles->first()?->name ?? '';

        // Header Metadata
        $data['headerRole'] = $data['headerRole'] ?? \App\Support\RoleLabelFormatter::pdfTitleSuffix($role);
        $data['headerYear'] = $data['headerYear'] ?? $user->active_budget_year;

        if ($user->isDesa() && $user->area) {
            $data['headerVillage'] = $data['headerVillage'] ?? $user->area->name;
            $data['headerKecamatan'] = $data['headerKecamatan'] ?? $user->area->parent?->name;
        } elseif ($user->isKecamatan() && $user->area) {
            $data['headerKecamatan'] = $data['headerKecamatan'] ?? $user->area->name;
        }

        // Footer Metadata
        $area = $user->area;
        $data['footerPlace'] = $data['footerPlace'] ?? config('pdf.regional_identity.kecamatan', 'Pecalungan');
        $data['footerDate'] = $data['footerDate'] ?? now()->translatedFormat('d F Y');
        $data['footerUserName'] = $data['footerUserName'] ?? $user->name;
        $data['footerRoleLabel'] = $data['footerRoleLabel'] ?? strtoupper(\App\Support\RoleLabelFormatter::label($role));

        // Database Driven Chairperson Metadata
        if ($area) {
            $data['footerChairpersonName'] = $data['footerChairpersonName'] ?? $area->chairperson_name;
            $data['footerChairpersonRole'] = $data['footerChairpersonRole'] ?? $area->chairperson_role;
        }

        // Fallback logic if database values are missing
        if ($user->isDesa()) {
            $data['footerChairpersonRole'] = $data['footerChairpersonRole'] ?? 'KETUA TP PKK DESA ' . strtoupper($area?->name ?? '');
        } else {
            $data['footerChairpersonRole'] = $data['footerChairpersonRole'] ?? 'KETUA TP PKK KECAMATAN ' . strtoupper(config('pdf.regional_identity.kecamatan', 'PECALUNGAN'));
        }

        $data['footerChairpersonName'] = $data['footerChairpersonName'] ?? '..........................';

        return $data;
    }
}
