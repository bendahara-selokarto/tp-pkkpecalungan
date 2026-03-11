<?php

namespace App\Domains\Wilayah\TutorKhusus\Controllers;

use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Domains\Wilayah\TutorKhusus\Actions\CreateScopedTutorKhususAction;
use App\Domains\Wilayah\TutorKhusus\Actions\UpdateTutorKhususAction;
use App\Domains\Wilayah\TutorKhusus\Models\TutorKhusus;
use App\Domains\Wilayah\TutorKhusus\Repositories\TutorKhususRepositoryInterface;
use App\Domains\Wilayah\TutorKhusus\Requests\ListTutorKhususRequest;
use App\Domains\Wilayah\TutorKhusus\Requests\StoreTutorKhususRequest;
use App\Domains\Wilayah\TutorKhusus\Requests\UpdateTutorKhususRequest;
use App\Domains\Wilayah\TutorKhusus\UseCases\GetScopedTutorKhususUseCase;
use App\Domains\Wilayah\TutorKhusus\UseCases\ListScopedTutorKhususUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class KecamatanTutorKhususController extends Controller
{
    public function __construct(
        private readonly TutorKhususRepositoryInterface $tutorKhususRepository,
        private readonly ListScopedTutorKhususUseCase $listScopedTutorKhususUseCase,
        private readonly GetScopedTutorKhususUseCase $getScopedTutorKhususUseCase,
        private readonly CreateScopedTutorKhususAction $createScopedTutorKhususAction,
        private readonly UpdateTutorKhususAction $updateTutorKhususAction
    ) {
        $this->middleware('scope.role:kecamatan');
    }

    public function index(ListTutorKhususRequest $request): Response
    {
        $this->authorize('viewAny', TutorKhusus::class);
        $items = $this->listScopedTutorKhususUseCase->execute(ScopeLevel::KECAMATAN->value, $request->perPage());

        return Inertia::render('Kecamatan/TutorKhusus/Index', [
            'tutorKhususItems' => $items->through(fn (TutorKhusus $item) => [
                'id' => $item->id,
                'jenis_tutor' => $item->jenis_tutor,
                'jumlah_tutor' => $item->jumlah_tutor,
                'keterangan' => $item->keterangan,
                'tahun_anggaran' => $item->tahun_anggaran,
            ]),
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
        $this->authorize('create', TutorKhusus::class);

        return Inertia::render('Kecamatan/TutorKhusus/Create');
    }

    public function store(StoreTutorKhususRequest $request): RedirectResponse
    {
        $this->authorize('create', TutorKhusus::class);
        $this->createScopedTutorKhususAction->execute($request->validated(), ScopeLevel::KECAMATAN->value);

        return redirect()->route('kecamatan.tutor-khusus.index')->with('success', 'Data tutor khusus berhasil dibuat');
    }

    public function show(int $id): Response
    {
        $tutorKhusus = $this->getScopedTutorKhususUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('view', $tutorKhusus);

        return Inertia::render('Kecamatan/TutorKhusus/Show', [
            'tutorKhusus' => [
                'id' => $tutorKhusus->id,
                'jenis_tutor' => $tutorKhusus->jenis_tutor,
                'jumlah_tutor' => $tutorKhusus->jumlah_tutor,
                'keterangan' => $tutorKhusus->keterangan,
                'tahun_anggaran' => $tutorKhusus->tahun_anggaran,
            ],
        ]);
    }

    public function edit(int $id): Response
    {
        $tutorKhusus = $this->getScopedTutorKhususUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('update', $tutorKhusus);

        return Inertia::render('Kecamatan/TutorKhusus/Edit', [
            'tutorKhusus' => [
                'id' => $tutorKhusus->id,
                'jenis_tutor' => $tutorKhusus->jenis_tutor,
                'jumlah_tutor' => $tutorKhusus->jumlah_tutor,
                'keterangan' => $tutorKhusus->keterangan,
                'tahun_anggaran' => $tutorKhusus->tahun_anggaran,
            ],
        ]);
    }

    public function update(UpdateTutorKhususRequest $request, int $id): RedirectResponse
    {
        $tutorKhusus = $this->getScopedTutorKhususUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('update', $tutorKhusus);
        $this->updateTutorKhususAction->execute($tutorKhusus, $request->validated());

        return redirect()->route('kecamatan.tutor-khusus.index')->with('success', 'Data tutor khusus berhasil diperbarui');
    }

    public function destroy(int $id): RedirectResponse
    {
        $tutorKhusus = $this->getScopedTutorKhususUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('delete', $tutorKhusus);
        $this->tutorKhususRepository->delete($tutorKhusus);

        return redirect()->route('kecamatan.tutor-khusus.index')->with('success', 'Data tutor khusus berhasil dihapus');
    }
}
