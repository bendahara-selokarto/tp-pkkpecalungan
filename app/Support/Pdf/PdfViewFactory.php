<?php

namespace App\Support\Pdf;

use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DompdfPdf;
use InvalidArgumentException;

class PdfViewFactory
{
    public const PAPER_SIZE_A4 = 'a4';
    public const ORIENTATION_LANDSCAPE = 'landscape';
    public const ORIENTATION_PORTRAIT = 'portrait';

    public function loadView(
        string $view,
        array $data = [],
        ?string $orientation = null,
        string $paperSize = self::PAPER_SIZE_A4
    ): DompdfPdf {
        $resolvedOrientation = $orientation ?? self::ORIENTATION_LANDSCAPE;

        if (!in_array($resolvedOrientation, [self::ORIENTATION_LANDSCAPE, self::ORIENTATION_PORTRAIT], true)) {
            throw new InvalidArgumentException('Invalid PDF orientation. Use landscape or portrait.');
        }

        return Pdf::loadView($view, $data)->setPaper($paperSize, $resolvedOrientation);
    }
}
