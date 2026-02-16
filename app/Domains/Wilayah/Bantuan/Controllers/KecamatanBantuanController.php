<?php

namespace App\Domains\Wilayah\Bantuan\Controllers;

use App\Domains\Wilayah\Bantuan\Actions\CreateScopedBantuanAction;
use App\Domains\Wilayah\Bantuan\Actions\UpdateBantuanAction;
use App\Domains\Wilayah\Bantuan\Models\Bantuan;
use App\Domains\Wilayah\Bantuan\Repositories\BantuanRepository;
use App\Domains\Wilayah\Bantuan\Requests\StoreBantuanRequest;
use App\Domains\Wilayah\Bantuan\Requests\UpdateBantuanRequest;
use App\Domains\Wilayah\Bantuan\UseCases\GetScopedBantuanUseCase;
use App\Domains\Wilayah\Bantuan\UseCases\ListScopedBantuanUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class KecamatanBantuanController extends Controller
{
    public function __construct(
        private readonly BantuanRepository $bantuanRepository,
        private readonly ListScopedBantuanUseCase $listScopedBantuanUseCase,
        private readonly GetScopedBantuanUseCase $getScopedBantuanUseCase,
        private readonly CreateScopedBantuanAction $createScopedBantuanAction,
        private readonly UpdateBantuanAction $updateBantuanAction
    ) {
        $this->middleware('role:admin-kecamatan');
    }

    public function index(): Response
    {
        $this->authorize('viewAny', Bantuan::class);
        $bantuans = $this->listScopedBantuanUseCase->execute('kecamatan');

        return Inertia::render('Kecamatan/Bantuan/Index', [
            'bantuans' => $bantuans->values()->map(fn (Bantuan $item) => [
                'id' => $item->id,
                'name' => $item->name,
                'category' => $item->category,
                'description' => $item->description,
                'source' => $item->source,
                'amount' => $item->amount,
                'received_date' => $item->received_date,
            ]),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Bantuan::class);

        return Inertia::render('Kecamatan/Bantuan/Create');
    }

    public function store(StoreBantuanRequest $request): RedirectResponse
    {
        $this->authorize('create', Bantuan::class);
        $this->createScopedBantuanAction->execute($request->validated(), 'kecamatan');

        return redirect()->route('kecamatan.bantuans.index')->with('success', 'Data bantuan berhasil dibuat');
    }

    public function show(int $id): Response
    {
        $bantuan = $this->getScopedBantuanUseCase->execute($id, 'kecamatan');
        $this->authorize('view', $bantuan);

        return Inertia::render('Kecamatan/Bantuan/Show', [
            'bantuan' => [
                'id' => $bantuan->id,
                'name' => $bantuan->name,
                'category' => $bantuan->category,
                'description' => $bantuan->description,
                'source' => $bantuan->source,
                'amount' => $bantuan->amount,
                'received_date' => $bantuan->received_date,
            ],
        ]);
    }

    public function edit(int $id): Response
    {
        $bantuan = $this->getScopedBantuanUseCase->execute($id, 'kecamatan');
        $this->authorize('update', $bantuan);

        return Inertia::render('Kecamatan/Bantuan/Edit', [
            'bantuan' => [
                'id' => $bantuan->id,
                'name' => $bantuan->name,
                'category' => $bantuan->category,
                'description' => $bantuan->description,
                'source' => $bantuan->source,
                'amount' => $bantuan->amount,
                'received_date' => $bantuan->received_date,
            ],
        ]);
    }

    public function update(UpdateBantuanRequest $request, int $id): RedirectResponse
    {
        $bantuan = $this->getScopedBantuanUseCase->execute($id, 'kecamatan');
        $this->authorize('update', $bantuan);
        $this->updateBantuanAction->execute($bantuan, $request->validated());

        return redirect()->route('kecamatan.bantuans.index')->with('success', 'Data bantuan berhasil diupdate');
    }

    public function destroy(int $id): RedirectResponse
    {
        $bantuan = $this->getScopedBantuanUseCase->execute($id, 'kecamatan');
        $this->authorize('delete', $bantuan);
        $this->bantuanRepository->delete($bantuan);

        return redirect()->route('kecamatan.bantuans.index')->with('success', 'Data bantuan berhasil dihapus');
    }
}
