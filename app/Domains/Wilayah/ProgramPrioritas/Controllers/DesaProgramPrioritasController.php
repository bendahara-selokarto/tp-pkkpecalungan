<?php

namespace App\Domains\Wilayah\ProgramPrioritas\Controllers;

use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Domains\Wilayah\ProgramPrioritas\Actions\CreateScopedProgramPrioritasAction;
use App\Domains\Wilayah\ProgramPrioritas\Actions\UpdateProgramPrioritasAction;
use App\Domains\Wilayah\ProgramPrioritas\Models\ProgramPrioritas;
use App\Domains\Wilayah\ProgramPrioritas\Repositories\ProgramPrioritasRepositoryInterface;
use App\Domains\Wilayah\ProgramPrioritas\Requests\StoreProgramPrioritasRequest;
use App\Domains\Wilayah\ProgramPrioritas\Requests\UpdateProgramPrioritasRequest;
use App\Domains\Wilayah\ProgramPrioritas\UseCases\GetScopedProgramPrioritasUseCase;
use App\Domains\Wilayah\ProgramPrioritas\UseCases\ListScopedProgramPrioritasUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class DesaProgramPrioritasController extends Controller
{
    public function __construct(
        private readonly ProgramPrioritasRepositoryInterface $programPrioritasRepository,
        private readonly ListScopedProgramPrioritasUseCase $listScopedProgramPrioritasUseCase,
        private readonly GetScopedProgramPrioritasUseCase $getScopedProgramPrioritasUseCase,
        private readonly CreateScopedProgramPrioritasAction $createScopedProgramPrioritasAction,
        private readonly UpdateProgramPrioritasAction $updateProgramPrioritasAction
    ) {
        $this->middleware('scope.role:desa');
    }

    public function index(): Response
    {
        $this->authorize('viewAny', ProgramPrioritas::class);
        $items = $this->listScopedProgramPrioritasUseCase->execute(ScopeLevel::DESA->value);

        return Inertia::render('Desa/ProgramPrioritas/Index', [
            'programPrioritas' => $items->values()->map(fn (ProgramPrioritas $item) => [
                'id' => $item->id,
                'program' => $item->program,
                'prioritas_program' => $item->prioritas_program,
                'kegiatan' => $item->kegiatan,
                'sasaran_target' => $item->sasaran_target,
                'jadwal_i' => (bool) $item->jadwal_i,
                'jadwal_ii' => (bool) $item->jadwal_ii,
                'jadwal_iii' => (bool) $item->jadwal_iii,
                'jadwal_iv' => (bool) $item->jadwal_iv,
                'sumber_dana_pusat' => (bool) $item->sumber_dana_pusat,
                'sumber_dana_apbd' => (bool) $item->sumber_dana_apbd,
                'sumber_dana_swd' => (bool) $item->sumber_dana_swd,
                'sumber_dana_bant' => (bool) $item->sumber_dana_bant,
                'keterangan' => $item->keterangan,
            ]),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', ProgramPrioritas::class);

        return Inertia::render('Desa/ProgramPrioritas/Create');
    }

    public function store(StoreProgramPrioritasRequest $request): RedirectResponse
    {
        $this->authorize('create', ProgramPrioritas::class);
        $this->createScopedProgramPrioritasAction->execute($request->validated(), ScopeLevel::DESA->value);

        return redirect()->route('desa.program-prioritas.index')->with('success', 'Data program prioritas berhasil dibuat');
    }

    public function show(int $id): Response
    {
        $programPrioritas = $this->getScopedProgramPrioritasUseCase->execute($id, ScopeLevel::DESA->value);
        $this->authorize('view', $programPrioritas);

        return Inertia::render('Desa/ProgramPrioritas/Show', [
            'programPrioritas' => [
                'id' => $programPrioritas->id,
                'program' => $programPrioritas->program,
                'prioritas_program' => $programPrioritas->prioritas_program,
                'kegiatan' => $programPrioritas->kegiatan,
                'sasaran_target' => $programPrioritas->sasaran_target,
                'jadwal_i' => (bool) $programPrioritas->jadwal_i,
                'jadwal_ii' => (bool) $programPrioritas->jadwal_ii,
                'jadwal_iii' => (bool) $programPrioritas->jadwal_iii,
                'jadwal_iv' => (bool) $programPrioritas->jadwal_iv,
                'sumber_dana_pusat' => (bool) $programPrioritas->sumber_dana_pusat,
                'sumber_dana_apbd' => (bool) $programPrioritas->sumber_dana_apbd,
                'sumber_dana_swd' => (bool) $programPrioritas->sumber_dana_swd,
                'sumber_dana_bant' => (bool) $programPrioritas->sumber_dana_bant,
                'keterangan' => $programPrioritas->keterangan,
            ],
        ]);
    }

    public function edit(int $id): Response
    {
        $programPrioritas = $this->getScopedProgramPrioritasUseCase->execute($id, ScopeLevel::DESA->value);
        $this->authorize('update', $programPrioritas);

        return Inertia::render('Desa/ProgramPrioritas/Edit', [
            'programPrioritas' => [
                'id' => $programPrioritas->id,
                'program' => $programPrioritas->program,
                'prioritas_program' => $programPrioritas->prioritas_program,
                'kegiatan' => $programPrioritas->kegiatan,
                'sasaran_target' => $programPrioritas->sasaran_target,
                'jadwal_i' => (bool) $programPrioritas->jadwal_i,
                'jadwal_ii' => (bool) $programPrioritas->jadwal_ii,
                'jadwal_iii' => (bool) $programPrioritas->jadwal_iii,
                'jadwal_iv' => (bool) $programPrioritas->jadwal_iv,
                'sumber_dana_pusat' => (bool) $programPrioritas->sumber_dana_pusat,
                'sumber_dana_apbd' => (bool) $programPrioritas->sumber_dana_apbd,
                'sumber_dana_swd' => (bool) $programPrioritas->sumber_dana_swd,
                'sumber_dana_bant' => (bool) $programPrioritas->sumber_dana_bant,
                'keterangan' => $programPrioritas->keterangan,
            ],
        ]);
    }

    public function update(UpdateProgramPrioritasRequest $request, int $id): RedirectResponse
    {
        $programPrioritas = $this->getScopedProgramPrioritasUseCase->execute($id, ScopeLevel::DESA->value);
        $this->authorize('update', $programPrioritas);
        $this->updateProgramPrioritasAction->execute($programPrioritas, $request->validated());

        return redirect()->route('desa.program-prioritas.index')->with('success', 'Data program prioritas berhasil diupdate');
    }

    public function destroy(int $id): RedirectResponse
    {
        $programPrioritas = $this->getScopedProgramPrioritasUseCase->execute($id, ScopeLevel::DESA->value);
        $this->authorize('delete', $programPrioritas);
        $this->programPrioritasRepository->delete($programPrioritas);

        return redirect()->route('desa.program-prioritas.index')->with('success', 'Data program prioritas berhasil dihapus');
    }
}
