<?php

namespace App\Http\Controllers;

use App\Domains\Wilayah\Arsip\UseCases\ListArsipDocumentsUseCase;
use App\Domains\Wilayah\Arsip\UseCases\ResolveArsipDocumentDownloadUseCase;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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
                'download_url' => route('arsip.download', ['document' => $document['name']], absolute: false),
            ])
            ->values()
            ->all();

        return Inertia::render('Arsip/Index', [
            'documents' => $documents,
        ]);
    }

    public function download(string $document): BinaryFileResponse
    {
        $resolvedDocument = $this->resolveArsipDocumentDownloadUseCase->execute($document);
        abort_if(! is_array($resolvedDocument), 404);

        return response()->download(
            $resolvedDocument['path'],
            $resolvedDocument['name']
        );
    }
}
