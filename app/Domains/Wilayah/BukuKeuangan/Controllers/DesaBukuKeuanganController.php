<?php

namespace App\Domains\Wilayah\BukuKeuangan\Controllers;

use App\Domains\Wilayah\BukuKeuangan\Actions\CreateScopedBukuKeuanganAction;
use App\Domains\Wilayah\BukuKeuangan\Actions\UpdateBukuKeuanganAction;
use App\Domains\Wilayah\BukuKeuangan\Models\BukuKeuangan;
use App\Domains\Wilayah\BukuKeuangan\Repositories\BukuKeuanganRepositoryInterface;
use App\Domains\Wilayah\BukuKeuangan\Requests\ListBukuKeuanganRequest;
use App\Domains\Wilayah\BukuKeuangan\Requests\StoreBukuKeuanganRequest;
use App\Domains\Wilayah\BukuKeuangan\Requests\UpdateBukuKeuanganRequest;
use App\Domains\Wilayah\BukuKeuangan\UseCases\GetScopedBukuKeuanganUseCase;
use App\Domains\Wilayah\BukuKeuangan\UseCases\ListScopedBukuKeuanganUseCase;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class DesaBukuKeuanganController extends Controller
{
    public function __construct(
        private readonly BukuKeuanganRepositoryInterface $bukuKeuanganRepository,
        private readonly ListScopedBukuKeuanganUseCase $listScopedBukuKeuanganUseCase,
        private readonly GetScopedBukuKeuanganUseCase $getScopedBukuKeuanganUseCase,
        private readonly CreateScopedBukuKeuanganAction $createScopedBukuKeuanganAction,
        private readonly UpdateBukuKeuanganAction $updateBukuKeuanganAction
    ) {
        $this->middleware('scope.role:desa');
    }

    public function index(ListBukuKeuanganRequest $request): Response
    {
        $this->authorize('viewAny', BukuKeuangan::class);
        $items = $this->listScopedBukuKeuanganUseCase
            ->execute('desa', $request->perPage())
            ->through(fn (BukuKeuangan $item) => [
                'id' => $item->id,
                'transaction_date' => $this->formatDateForPayload($item->transaction_date?->format('Y-m-d')),
                'source' => $item->source,
                'description' => $item->description,
                'reference_number' => $item->reference_number,
                'entry_type' => $item->entry_type,
                'amount' => $item->amount,
            ]);

        return Inertia::render('Desa/BukuKeuangan/Index', [
            'entries' => $items,
            'pagination' => [
                'perPageOptions' => [10, 25, 50],
            ],
            'filters' => [
                'per_page' => $request->perPage(),
            ],
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', BukuKeuangan::class);

        return Inertia::render('Desa/BukuKeuangan/Create');
    }

    public function store(StoreBukuKeuanganRequest $request): RedirectResponse
    {
        $this->authorize('create', BukuKeuangan::class);
        $this->createScopedBukuKeuanganAction->execute($request->validated(), 'desa');

        return redirect()->route('desa.buku-keuangan.index')->with('success', 'Transaksi buku keuangan berhasil dibuat');
    }

    public function show(int $id): Response
    {
        $bukuKeuangan = $this->getScopedBukuKeuanganUseCase->execute($id, 'desa');
        $this->authorize('view', $bukuKeuangan);

        return Inertia::render('Desa/BukuKeuangan/Show', [
            'entry' => [
                'id' => $bukuKeuangan->id,
                'transaction_date' => $this->formatDateForPayload($bukuKeuangan->transaction_date?->format('Y-m-d')),
                'source' => $bukuKeuangan->source,
                'description' => $bukuKeuangan->description,
                'reference_number' => $bukuKeuangan->reference_number,
                'entry_type' => $bukuKeuangan->entry_type,
                'amount' => $bukuKeuangan->amount,
            ],
        ]);
    }

    public function edit(int $id): Response
    {
        $bukuKeuangan = $this->getScopedBukuKeuanganUseCase->execute($id, 'desa');
        $this->authorize('update', $bukuKeuangan);

        return Inertia::render('Desa/BukuKeuangan/Edit', [
            'entry' => [
                'id' => $bukuKeuangan->id,
                'transaction_date' => $this->formatDateForPayload($bukuKeuangan->transaction_date?->format('Y-m-d')),
                'source' => $bukuKeuangan->source,
                'description' => $bukuKeuangan->description,
                'reference_number' => $bukuKeuangan->reference_number,
                'entry_type' => $bukuKeuangan->entry_type,
                'amount' => $bukuKeuangan->amount,
            ],
        ]);
    }

    public function update(UpdateBukuKeuanganRequest $request, int $id): RedirectResponse
    {
        $bukuKeuangan = $this->getScopedBukuKeuanganUseCase->execute($id, 'desa');
        $this->authorize('update', $bukuKeuangan);
        $this->updateBukuKeuanganAction->execute($bukuKeuangan, $request->validated());

        return redirect()->route('desa.buku-keuangan.index')->with('success', 'Transaksi buku keuangan berhasil diupdate');
    }

    public function destroy(int $id): RedirectResponse
    {
        $bukuKeuangan = $this->getScopedBukuKeuanganUseCase->execute($id, 'desa');
        $this->authorize('delete', $bukuKeuangan);
        $this->bukuKeuanganRepository->delete($bukuKeuangan);

        return redirect()->route('desa.buku-keuangan.index')->with('success', 'Transaksi buku keuangan berhasil dihapus');
    }

    private function formatDateForPayload(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        return Carbon::parse($value)->format('Y-m-d');
    }
}
