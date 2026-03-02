<?php

namespace App\Domains\Wilayah\ProgramPrioritas\Controllers;

use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Domains\Wilayah\ProgramPrioritas\Actions\CreateScopedProgramPrioritasAction;
use App\Domains\Wilayah\ProgramPrioritas\Actions\UpdateProgramPrioritasAction;
use App\Domains\Wilayah\ProgramPrioritas\Models\ProgramPrioritas;
use App\Domains\Wilayah\ProgramPrioritas\Repositories\ProgramPrioritasRepositoryInterface;
use App\Domains\Wilayah\ProgramPrioritas\Requests\ListProgramPrioritasRequest;
use App\Domains\Wilayah\ProgramPrioritas\Requests\StoreProgramPrioritasRequest;
use App\Domains\Wilayah\ProgramPrioritas\Requests\UpdateProgramPrioritasRequest;
use App\Domains\Wilayah\ProgramPrioritas\UseCases\GetScopedProgramPrioritasUseCase;
use App\Domains\Wilayah\ProgramPrioritas\UseCases\ListScopedProgramPrioritasUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class KecamatanProgramPrioritasController extends Controller
{
    public function __construct(
        private readonly ProgramPrioritasRepositoryInterface $programPrioritasRepository,
        private readonly ListScopedProgramPrioritasUseCase $listScopedProgramPrioritasUseCase,
        private readonly GetScopedProgramPrioritasUseCase $getScopedProgramPrioritasUseCase,
        private readonly CreateScopedProgramPrioritasAction $createScopedProgramPrioritasAction,
        private readonly UpdateProgramPrioritasAction $updateProgramPrioritasAction
    ) {
        $this->middleware('scope.role:kecamatan');
    }

    public function index(ListProgramPrioritasRequest $request): Response
    {
        $this->authorize('viewAny', ProgramPrioritas::class);
        $items = $this->listScopedProgramPrioritasUseCase
            ->execute(ScopeLevel::KECAMATAN->value, $request->perPage())
            ->through(fn (ProgramPrioritas $item) => $this->serializeProgramPrioritas($item));

        return Inertia::render('Kecamatan/ProgramPrioritas/Index', [
            'programPrioritas' => $items,
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
        $this->authorize('create', ProgramPrioritas::class);

        return Inertia::render('Kecamatan/ProgramPrioritas/Create');
    }

    public function store(StoreProgramPrioritasRequest $request): RedirectResponse
    {
        $this->authorize('create', ProgramPrioritas::class);
        $this->createScopedProgramPrioritasAction->execute($request->validated(), ScopeLevel::KECAMATAN->value);

        return redirect()->route('kecamatan.program-prioritas.index')->with('success', 'Data program prioritas berhasil dibuat');
    }

    public function show(int $id): Response
    {
        $programPrioritas = $this->getScopedProgramPrioritasUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('view', $programPrioritas);

        return Inertia::render('Kecamatan/ProgramPrioritas/Show', [
            'programPrioritas' => $this->serializeProgramPrioritas($programPrioritas),
        ]);
    }

    public function edit(int $id): Response
    {
        $programPrioritas = $this->getScopedProgramPrioritasUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('update', $programPrioritas);

        return Inertia::render('Kecamatan/ProgramPrioritas/Edit', [
            'programPrioritas' => $this->serializeProgramPrioritas($programPrioritas),
        ]);
    }

    public function update(UpdateProgramPrioritasRequest $request, int $id): RedirectResponse
    {
        $programPrioritas = $this->getScopedProgramPrioritasUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('update', $programPrioritas);
        $this->updateProgramPrioritasAction->execute($programPrioritas, $request->validated());

        return redirect()->route('kecamatan.program-prioritas.index')->with('success', 'Data program prioritas berhasil diupdate');
    }

    public function destroy(int $id): RedirectResponse
    {
        $programPrioritas = $this->getScopedProgramPrioritasUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('delete', $programPrioritas);
        $this->programPrioritasRepository->delete($programPrioritas);

        return redirect()->route('kecamatan.program-prioritas.index')->with('success', 'Data program prioritas berhasil dihapus');
    }

    /**
     * @return array<string, mixed>
     */
    private function serializeProgramPrioritas(ProgramPrioritas $item): array
    {
        return [
            'id' => $item->id,
            'program' => $item->program,
            'prioritas_program' => $item->prioritas_program,
            'kegiatan' => $item->kegiatan,
            'sasaran_target' => $item->sasaran_target,
            'jadwal_bulan_1' => (bool) $item->jadwal_bulan_1,
            'jadwal_bulan_2' => (bool) $item->jadwal_bulan_2,
            'jadwal_bulan_3' => (bool) $item->jadwal_bulan_3,
            'jadwal_bulan_4' => (bool) $item->jadwal_bulan_4,
            'jadwal_bulan_5' => (bool) $item->jadwal_bulan_5,
            'jadwal_bulan_6' => (bool) $item->jadwal_bulan_6,
            'jadwal_bulan_7' => (bool) $item->jadwal_bulan_7,
            'jadwal_bulan_8' => (bool) $item->jadwal_bulan_8,
            'jadwal_bulan_9' => (bool) $item->jadwal_bulan_9,
            'jadwal_bulan_10' => (bool) $item->jadwal_bulan_10,
            'jadwal_bulan_11' => (bool) $item->jadwal_bulan_11,
            'jadwal_bulan_12' => (bool) $item->jadwal_bulan_12,
            'jadwal_i' => (bool) $item->jadwal_i,
            'jadwal_ii' => (bool) $item->jadwal_ii,
            'jadwal_iii' => (bool) $item->jadwal_iii,
            'jadwal_iv' => (bool) $item->jadwal_iv,
            'sumber_dana_pusat' => (bool) $item->sumber_dana_pusat,
            'sumber_dana_apbd' => (bool) $item->sumber_dana_apbd,
            'sumber_dana_swd' => (bool) $item->sumber_dana_swd,
            'sumber_dana_bant' => (bool) $item->sumber_dana_bant,
            'keterangan' => $item->keterangan,
        ];
    }
}

