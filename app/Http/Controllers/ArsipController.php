<?php

namespace App\Http\Controllers;

use App\Domains\Wilayah\Arsip\Actions\CreateArsipDocumentAction;
use App\Domains\Wilayah\Arsip\Actions\DeleteArsipDocumentAction;
use App\Domains\Wilayah\Arsip\Actions\UpdateArsipDocumentAction;
use App\Domains\Wilayah\Arsip\Models\ArsipDocument;
use App\Domains\Wilayah\Arsip\UseCases\ListArsipDocumentsUseCase;
use App\Domains\Wilayah\Arsip\UseCases\ResolveArsipDocumentDownloadUseCase;
use App\Http\Requests\Arsip\StoreArsipDocumentRequest;
use App\Http\Requests\Arsip\UpdateArsipDocumentRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class ArsipController extends Controller
{
    public function __construct(
        private readonly ListArsipDocumentsUseCase $listArsipDocumentsUseCase,
        private readonly ResolveArsipDocumentDownloadUseCase $resolveArsipDocumentDownloadUseCase,
        private readonly CreateArsipDocumentAction $createArsipDocumentAction,
        private readonly UpdateArsipDocumentAction $updateArsipDocumentAction,
        private readonly DeleteArsipDocumentAction $deleteArsipDocumentAction
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $user = $request->user();
        abort_if(! $user, 403);

        $documents = collect($this->listArsipDocumentsUseCase->execute($user))
            ->map(static fn (array $document): array => [
                ...$document,
                'download_url' => route('arsip.download', ['arsipDocument' => $document['id']], absolute: false),
            ])
            ->values()
            ->all();

        return Inertia::render('Arsip/Index', [
            'documents' => $documents,
            'can' => [
                'upload' => $user->can('create', ArsipDocument::class),
            ],
        ]);
    }

    public function store(StoreArsipDocumentRequest $request): RedirectResponse
    {
        $this->createArsipDocumentAction->execute($request->validated(), $request->user());

        return redirect()
            ->route('arsip.index')
            ->with('success', 'Dokumen arsip berhasil diunggah.');
    }

    public function update(UpdateArsipDocumentRequest $request, ArsipDocument $arsipDocument): RedirectResponse
    {
        $user = $request->user();
        abort_if(! $user, 403);
        $this->ensureOwnedDocument($arsipDocument, (int) $user->id);

        $this->updateArsipDocumentAction->execute($arsipDocument, $request->validated(), $user);

        return redirect()
            ->route('arsip.index')
            ->with('success', 'Dokumen arsip berhasil diperbarui.');
    }

    public function destroy(Request $request, ArsipDocument $arsipDocument): RedirectResponse
    {
        $user = $request->user();
        abort_if(! $user, 403);
        $this->ensureOwnedDocument($arsipDocument, (int) $user->id);

        $this->deleteArsipDocumentAction->execute($arsipDocument);

        return redirect()
            ->route('arsip.index')
            ->with('success', 'Dokumen arsip berhasil dihapus.');
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

    private function ensureOwnedDocument(ArsipDocument $arsipDocument, int $userId): void
    {
        abort_unless((int) $arsipDocument->created_by === $userId, 403);
    }
}
