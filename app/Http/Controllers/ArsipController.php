<?php

namespace App\Http\Controllers;

use App\Domains\Wilayah\Arsip\Models\ArsipDocument;
use App\Domains\Wilayah\Arsip\UseCases\ListArsipDocumentsUseCase;
use App\Domains\Wilayah\Arsip\UseCases\ResolveArsipDocumentDownloadUseCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class ArsipController extends Controller
{
    public function __construct(
        private readonly ListArsipDocumentsUseCase $listArsipDocumentsUseCase,
        private readonly ResolveArsipDocumentDownloadUseCase $resolveArsipDocumentDownloadUseCase
    ) {
    }

    public function __invoke(): Response
    {
        $documents = collect($this->listArsipDocumentsUseCase->execute())
            ->map(static fn (array $document): array => [
                ...$document,
                'download_url' => route('arsip.download', ['arsipDocument' => $document['id']], absolute: false),
            ])
            ->values()
            ->all();

        return Inertia::render('Arsip/Index', [
            'documents' => $documents,
        ]);
    }

    public function download(Request $request, ArsipDocument $arsipDocument): SymfonyResponse
    {
        $user = $request->user();
        abort_if(! $user, 403);

        $resolvedDocument = $this->resolveArsipDocumentDownloadUseCase->execute($user, $arsipDocument);
        abort_if(! is_array($resolvedDocument), 404);
        abort_if(! Storage::disk('public')->exists($resolvedDocument['storage_path']), 404);

        return Storage::disk('public')->download(
            $resolvedDocument['storage_path'],
            $resolvedDocument['download_name']
        );
    }
}
