<?php

namespace App\Domains\Wilayah\Activities\Controllers;

use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\Activities\UseCases\GetKecamatanDesaActivityUseCase;
use App\Domains\Wilayah\Activities\UseCases\GetScopedActivityUseCase;
use App\Domains\Wilayah\Activities\UseCases\ListScopedActivitiesUseCase;
use App\Http\Controllers\Controller;
use App\Support\Pdf\PdfViewFactory;
use Symfony\Component\HttpFoundation\Response;

class ActivityPrintController extends Controller
{
    public function __construct(
        private readonly GetScopedActivityUseCase $getScopedActivityUseCase,
        private readonly GetKecamatanDesaActivityUseCase $getKecamatanDesaActivityUseCase,
        private readonly ListScopedActivitiesUseCase $listScopedActivitiesUseCase,
        private readonly PdfViewFactory $pdfViewFactory
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

    public function printDesaReport(): Response
    {
        return $this->streamReport(ScopeLevel::DESA->value);
    }

    public function printKecamatanReport(): Response
    {
        return $this->streamReport(ScopeLevel::KECAMATAN->value);
    }

    private function streamPdf(Activity $activity, string $context): Response
    {
        $pdf = $this->pdfViewFactory->loadView('pdf.activity', [
            'activity' => $activity,
            'printedBy' => auth()->user(),
            'printedAt' => now(),
        ]);

        return $pdf->stream("activity-{$context}-{$activity->id}.pdf");
    }

    private function streamReport(string $level): Response
    {
        $this->authorize('viewAny', Activity::class);

        $items = $this->listScopedActivitiesUseCase
            ->executeAll($level)
            ->sortBy('id')
            ->values();

        $user = auth()->user()->loadMissing('area');
        $pdf = $this->pdfViewFactory->loadView('pdf.activity_all_report', [
            'items' => $items,
            'level' => $level,
            'areaName' => $user->area?->name ?? '-',
            'printedBy' => $user,
            'printedAt' => now(),
        ]);

        return $pdf->stream("activity-{$level}-report.pdf");
    }
}
