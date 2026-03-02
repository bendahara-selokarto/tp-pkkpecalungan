<?php

namespace App\Domains\Wilayah\Arsip\Controllers;

use App\Domains\Wilayah\Arsip\Models\ArsipDocument;
use App\Domains\Wilayah\Arsip\Requests\ListKecamatanDesaArsipRequest;
use App\Domains\Wilayah\Arsip\UseCases\ListKecamatanDesaArsipDocumentsUseCase;
use App\Domains\Wilayah\Repositories\AreaRepositoryInterface;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class KecamatanDesaArsipController extends Controller
{
    public function __construct(
        private readonly ListKecamatanDesaArsipDocumentsUseCase $listKecamatanDesaArsipDocumentsUseCase,
        private readonly AreaRepositoryInterface $areaRepository
    ) {
        $this->middleware('scope.role:kecamatan');
    }

    public function index(ListKecamatanDesaArsipRequest $request): Response
    {
        $this->authorize('viewAny', ArsipDocument::class);

        $user = $request->user();
        abort_if(! $user || ! is_numeric($user->area_id), 403);

        $kecamatanAreaId = (int) $user->area_id;

        $documents = $this->listKecamatanDesaArsipDocumentsUseCase
            ->execute(
                $kecamatanAreaId,
                $request->perPage(),
                $request->desaId(),
                $request->keyword()
            )
            ->through(static fn (ArsipDocument $document): array => [
                'id' => (int) $document->id,
                'title' => (string) $document->title,
                'description' => $document->description,
                'original_name' => (string) $document->original_name,
                'extension' => strtoupper((string) $document->extension),
                'size_bytes' => (int) $document->size_bytes,
                'updated_at' => $document->updated_at?->toIso8601String(),
                'area' => $document->area
                    ? [
                        'id' => (int) $document->area->id,
                        'name' => (string) $document->area->name,
                    ]
                    : null,
                'creator' => $document->creator
                    ? [
                        'id' => (int) $document->creator->id,
                        'name' => (string) $document->creator->name,
                    ]
                    : null,
                'download_url' => route('arsip.download', ['arsipDocument' => $document->id], absolute: false),
            ]);

        $desaOptions = $this->areaRepository
            ->getDesaByKecamatan($kecamatanAreaId)
            ->map(static fn ($area) => [
                'id' => (int) $area->id,
                'name' => (string) $area->name,
            ])
            ->values();

        return Inertia::render('Kecamatan/DesaArsip/Index', [
            'documents' => $documents,
            'desaOptions' => $desaOptions,
            'pagination' => [
                'perPageOptions' => [10, 25, 50],
            ],
            'filters' => [
                'per_page' => $request->perPage(),
                'desa_id' => $request->desaId(),
                'q' => $request->keyword(),
            ],
        ]);
    }
}
