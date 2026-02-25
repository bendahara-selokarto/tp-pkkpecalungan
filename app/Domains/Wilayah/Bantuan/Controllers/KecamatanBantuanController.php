<?php

namespace App\Domains\Wilayah\Bantuan\Controllers;

use App\Domains\Wilayah\Bantuan\Actions\CreateScopedBantuanAction;
use App\Domains\Wilayah\Bantuan\Actions\UpdateBantuanAction;
use App\Domains\Wilayah\Bantuan\Models\Bantuan;
use App\Domains\Wilayah\Bantuan\Repositories\BantuanRepositoryInterface;
use App\Domains\Wilayah\Bantuan\Requests\ListBantuanRequest;
use App\Domains\Wilayah\Bantuan\Requests\StoreBantuanRequest;
use App\Domains\Wilayah\Bantuan\Requests\UpdateBantuanRequest;
use App\Domains\Wilayah\Bantuan\UseCases\GetScopedBantuanUseCase;
use App\Domains\Wilayah\Bantuan\UseCases\ListScopedBantuanUseCase;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class KecamatanBantuanController extends Controller
{
    public function __construct(
        private readonly BantuanRepositoryInterface $bantuanRepository,
        private readonly ListScopedBantuanUseCase $listScopedBantuanUseCase,
        private readonly GetScopedBantuanUseCase $getScopedBantuanUseCase,
        private readonly CreateScopedBantuanAction $createScopedBantuanAction,
        private readonly UpdateBantuanAction $updateBantuanAction
    ) {
        $this->middleware('scope.role:kecamatan');
    }

    public function index(ListBantuanRequest $request): Response
    {
        $this->authorize('viewAny', Bantuan::class);
        $bantuans = $this->listScopedBantuanUseCase
            ->execute('kecamatan', $request->perPage())
            ->through(fn (Bantuan $item) => $this->serializeBantuan($item));

        return Inertia::render('Kecamatan/Bantuan/Index', [
            'bantuans' => $bantuans,
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
            'bantuan' => $this->serializeBantuan($bantuan),
        ]);
    }

    public function edit(int $id): Response
    {
        $bantuan = $this->getScopedBantuanUseCase->execute($id, 'kecamatan');
        $this->authorize('update', $bantuan);

        return Inertia::render('Kecamatan/Bantuan/Edit', [
            'bantuan' => $this->serializeBantuan($bantuan),
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

    private function formatDateForPayload(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        return Carbon::parse($value)->format('Y-m-d');
    }

    /**
     * @return array<string, mixed>
     */
    private function serializeBantuan(Bantuan $item): array
    {
        return [
            'id' => $item->id,
            'lokasi_penerima' => $item->name,
            'jenis_bantuan' => $item->category,
            'keterangan' => $item->description,
            'asal_bantuan' => $item->source,
            'jumlah' => $item->amount,
            'tanggal' => $this->formatDateForPayload($item->received_date),
        ];
    }
}
