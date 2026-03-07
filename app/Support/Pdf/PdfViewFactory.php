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

        return Pdf::loadView($view, $data)->setPaper($resolvedPaperSize, $resolvedOrientation);
    }
}
