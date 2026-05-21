<?php

namespace App\Domains\Wilayah\BukuEkspedisi\Controllers;

use App\Domains\Wilayah\BukuEkspedisi\Actions\CreateBukuEkspedisiAction;
use App\Domains\Wilayah\BukuEkspedisi\Actions\DeleteBukuEkspedisiAction;
use App\Domains\Wilayah\BukuEkspedisi\Actions\UpdateBukuEkspedisiAction;
use App\Domains\Wilayah\BukuEkspedisi\Models\BukuEkspedisi;
use App\Domains\Wilayah\BukuEkspedisi\Repositories\BukuEkspedisiRepositoryInterface;
use App\Domains\Wilayah\BukuEkspedisi\Requests\ListBukuEkspedisiRequest;
use App\Domains\Wilayah\BukuEkspedisi\Requests\StoreBukuEkspedisiRequest;
use App\Domains\Wilayah\BukuEkspedisi\Requests\UpdateBukuEkspedisiRequest;
use App\Domains\Wilayah\BukuEkspedisi\UseCases\GetScopedBukuEkspedisiUseCase;
use App\Domains\Wilayah\BukuEkspedisi\UseCases\ListScopedBukuEkspedisiUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class DesaBukuEkspedisiController extends Controller
{
    public function __construct(
        private readonly BukuEkspedisiRepositoryInterface $bukuEkspedisiRepository,
        private readonly ListScopedBukuEkspedisiUseCase $listScopedBukuEkspedisiUseCase,
        private readonly GetScopedBukuEkspedisiUseCase $getScopedBukuEkspedisiUseCase,
        private readonly CreateBukuEkspedisiAction $createBukuEkspedisiAction,
        private readonly UpdateBukuEkspedisiAction $updateBukuEkspedisiAction,
        private readonly DeleteBukuEkspedisiAction $deleteBukuEkspedisiAction
    ) {
        $this->middleware('scope.role:desa');
    }

    public function index(ListBukuEkspedisiRequest $request): Response
    {
        $this->authorize('viewAny', BukuEkspedisi::class);
        $items = $this->listScopedBukuEkspedisiUseCase
            ->execute('desa', $request->perPage())
            ->through(fn (BukuEkspedisi $item) => [
                'id' => $item->id,
                'title' => $item->title,
                'original_name' => $item->original_name,
                'file_url' => Storage::disk('public')->url($item->file_path),
                'size_bytes' => $item->size_bytes,
                'created_at' => $item->created_at->format('Y-m-d H:i:s'),
            ]);

        return Inertia::render('Desa/BukuEkspedisi/Index', [
            'items' => $items,
            'pagination' => [
                'perPageOptions' => [10, 25, 50],
            ],
            'filters' => [
                'per_page' => $request->perPage(),
                'tahun_anggaran' => (int) $request->user()->active_budget_year,
            ],
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', BukuEkspedisi::class);

        return Inertia::render('Desa/BukuEkspedisi/Create');
    }

    public function store(StoreBukuEkspedisiRequest $request): RedirectResponse
    {
        $this->authorize('create', BukuEkspedisi::class);
        $this->createBukuEkspedisiAction->execute($request->user(), 'desa', $request->validated());

        return redirect()
            ->route('desa.buku-ekspedisi.index')
            ->with('success', 'Buku Ekspedisi berhasil dibuat');
    }

    public function edit(int $id): Response
    {
        $item = $this->getScopedBukuEkspedisiUseCase->execute($id, 'desa');
        $this->authorize('update', $item);

        return Inertia::render('Desa/BukuEkspedisi/Edit', [
            'item' => [
                'id' => $item->id,
                'title' => $item->title,
                'original_name' => $item->original_name,
                'file_url' => Storage::disk('public')->url($item->file_path),
            ],
        ]);
    }

    public function update(UpdateBukuEkspedisiRequest $request, int $id): RedirectResponse
    {
        $item = $this->getScopedBukuEkspedisiUseCase->execute($id, 'desa');
        $this->authorize('update', $item);
        $this->updateBukuEkspedisiAction->execute($item, $request->validated());

        return redirect()
            ->route('desa.buku-ekspedisi.index')
            ->with('success', 'Buku Ekspedisi berhasil diperbarui');
    }

    public function destroy(int $id): RedirectResponse
    {
        $item = $this->getScopedBukuEkspedisiUseCase->execute($id, 'desa');
        $this->authorize('delete', $item);
        $this->deleteBukuEkspedisiAction->execute($item);

        return redirect()
            ->route('desa.buku-ekspedisi.index')
            ->with('success', 'Buku Ekspedisi berhasil dihapus');
    }

    public function download(int $id)
    {
        $item = $this->getScopedBukuEkspedisiUseCase->execute($id, 'desa');
        $this->authorize('view', $item);

        return Storage::disk('public')->download($item->file_path, $item->original_name);
    }
}
