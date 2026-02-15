<?php

namespace App\Domains\Wilayah\Activities\Controllers;

use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\Activities\UseCases\GetKecamatanDesaActivityUseCase;
use App\Domains\Wilayah\Activities\UseCases\GetScopedActivityUseCase;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response;

class ActivityPrintController extends Controller
{
    public function __construct(
        private readonly GetScopedActivityUseCase $getScopedActivityUseCase,
        private readonly GetKecamatanDesaActivityUseCase $getKecamatanDesaActivityUseCase
    ) {
    }

    public function printDesa(int $id): Response
    {
        $activity = $this->getScopedActivityUseCase->execute($id, 'desa')->loadMissing(['area', 'creator']);
        $this->authorize('print', $activity);

        return $this->streamPdf($activity, 'desa');
    }

    public function printKecamatan(int $id): Response
    {
        $activity = $this->getScopedActivityUseCase->execute($id, 'kecamatan')->loadMissing(['area', 'creator']);
        $this->authorize('print', $activity);

        return $this->streamPdf($activity, 'kecamatan');
    }

    public function printKecamatanDesa(int $id): Response
    {
        $activity = $this->getKecamatanDesaActivityUseCase->execute($id);
        $this->authorize('print', $activity);

        return $this->streamPdf($activity, 'kecamatan-desa');
    }

    private function streamPdf(Activity $activity, string $context): Response
    {
        $pdf = Pdf::loadView('pdf.activity', [
            'activity' => $activity,
            'printedBy' => auth()->user(),
            'printedAt' => now(),
        ]);

        return $pdf->stream("activity-{$context}-{$activity->id}.pdf");
    }
}
