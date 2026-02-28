<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Domains\Wilayah\Arsip\Actions\CreateArsipDocumentAction;
use App\Domains\Wilayah\Arsip\Actions\DeleteArsipDocumentAction;
use App\Domains\Wilayah\Arsip\Actions\UpdateArsipDocumentAction;
use App\Domains\Wilayah\Arsip\Models\ArsipDocument;
use App\Domains\Wilayah\Arsip\UseCases\ListManagedArsipDocumentsUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\Arsip\StoreArsipDocumentRequest;
use App\Http\Requests\Arsip\UpdateArsipDocumentRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ArsipManagementController extends Controller
{
    public function __construct(
        private readonly ListManagedArsipDocumentsUseCase $listManagedArsipDocumentsUseCase,
        private readonly CreateArsipDocumentAction $createArsipDocumentAction,
        private readonly UpdateArsipDocumentAction $updateArsipDocumentAction,
        private readonly DeleteArsipDocumentAction $deleteArsipDocumentAction
    ) {
        $this->authorizeResource(ArsipDocument::class, 'arsipDocument');
    }

    public function index(): Response
    {
        return Inertia::render('SuperAdmin/Arsip/Index', [
            'documents' => $this->listManagedArsipDocumentsUseCase->execute(10),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('SuperAdmin/Arsip/Create');
    }

    public function store(StoreArsipDocumentRequest $request): RedirectResponse
    {
        $this->createArsipDocumentAction->execute($request->validated(), $request->user());

        return redirect()
            ->route('super-admin.arsip.index')
            ->with('success', 'Dokumen arsip berhasil ditambahkan.');
    }

    public function edit(ArsipDocument $arsipDocument): Response
    {
        return Inertia::render('SuperAdmin/Arsip/Edit', [
            'document' => [
                'id' => $arsipDocument->id,
                'title' => $arsipDocument->title,
                'description' => $arsipDocument->description,
                'original_name' => $arsipDocument->original_name,
                'extension' => strtoupper($arsipDocument->extension),
                'size_bytes' => (int) $arsipDocument->size_bytes,
                'is_published' => (bool) $arsipDocument->is_published,
                'published_at' => $arsipDocument->published_at?->toIso8601String(),
            ],
        ]);
    }

    public function update(UpdateArsipDocumentRequest $request, ArsipDocument $arsipDocument): RedirectResponse
    {
        $this->updateArsipDocumentAction->execute($arsipDocument, $request->validated(), $request->user());

        return redirect()
            ->route('super-admin.arsip.index')
            ->with('success', 'Dokumen arsip berhasil diperbarui.');
    }

    public function destroy(ArsipDocument $arsipDocument): RedirectResponse
    {
        $this->deleteArsipDocumentAction->execute($arsipDocument);

        return redirect()
            ->route('super-admin.arsip.index')
            ->with('success', 'Dokumen arsip berhasil dihapus.');
    }
}
