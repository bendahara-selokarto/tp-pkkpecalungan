<?php

namespace App\Domains\Wilayah\Activities\Controllers;

use App\Domains\Wilayah\Activities\Actions\CreateScopedActivityAction;
use App\Domains\Wilayah\Activities\Actions\UpdateActivityAction;
use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\Activities\Requests\StoreActivityRequest;
use App\Domains\Wilayah\Activities\Requests\UpdateActivityRequest;
use App\Domains\Wilayah\Activities\Repositories\ActivityRepositoryInterface;
use App\Domains\Wilayah\Activities\UseCases\GetScopedActivityUseCase;
use App\Domains\Wilayah\Activities\UseCases\ListScopedActivitiesUseCase;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class DesaActivityController extends Controller
{
    public function __construct(
        private readonly ActivityRepositoryInterface $activityRepository,
        private readonly ListScopedActivitiesUseCase $listScopedActivitiesUseCase,
        private readonly GetScopedActivityUseCase $getScopedActivityUseCase,
        private readonly CreateScopedActivityAction $createScopedActivityAction,
        private readonly UpdateActivityAction $updateActivityAction
    ) {
        $this->middleware('scope.role:desa');
    }

    public function index(): Response
    {
        $this->authorize('viewAny', Activity::class);
        $activities = $this->listScopedActivitiesUseCase->execute('desa');

        return Inertia::render('Desa/Activities/Index', [
            'activities' => $activities->map(fn (Activity $activity) => [
                'id' => $activity->id,
                'title' => $activity->title,
                'description' => $activity->description,
                'nama_petugas' => $activity->nama_petugas ?? $activity->title,
                'jabatan_petugas' => $activity->jabatan_petugas,
                'tempat_kegiatan' => $activity->tempat_kegiatan,
                'uraian' => $activity->uraian ?? $activity->description,
                'tanda_tangan' => $activity->tanda_tangan,
                'activity_date' => $activity->activity_date,
                'status' => $activity->status,
            ])->values(),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Activity::class);

        return Inertia::render('Desa/Activities/Create');
    }

    public function store(StoreActivityRequest $request): RedirectResponse
    {
        $this->authorize('create', Activity::class);
        $this->createScopedActivityAction->execute($request->validated(), 'desa');

        return redirect()->route('desa.activities.index')->with('success', 'Kegiatan berhasil dibuat');
    }

    public function show(int $id): Response
    {
        $activity = $this->getScopedActivityUseCase->execute($id, 'desa');
        $this->authorize('view', $activity);

        return Inertia::render('Desa/Activities/Show', [
            'activity' => [
                'id' => $activity->id,
                'title' => $activity->title,
                'description' => $activity->description,
                'nama_petugas' => $activity->nama_petugas ?? $activity->title,
                'jabatan_petugas' => $activity->jabatan_petugas,
                'tempat_kegiatan' => $activity->tempat_kegiatan,
                'uraian' => $activity->uraian ?? $activity->description,
                'tanda_tangan' => $activity->tanda_tangan,
                'activity_date' => $activity->activity_date,
                'status' => $activity->status,
            ],
            'can' => [
                'print' => auth()->user()->can('print', $activity),
            ],
            'routes' => [
                'print' => route('desa.activities.print', $activity->id),
            ],
        ]);
    }

    public function edit(int $id): Response
    {
        $activity = $this->getScopedActivityUseCase->execute($id, 'desa');
        $this->authorize('update', $activity);

        return Inertia::render('Desa/Activities/Edit', [
            'activity' => [
                'id' => $activity->id,
                'title' => $activity->title,
                'description' => $activity->description,
                'nama_petugas' => $activity->nama_petugas ?? $activity->title,
                'jabatan_petugas' => $activity->jabatan_petugas,
                'tempat_kegiatan' => $activity->tempat_kegiatan,
                'uraian' => $activity->uraian ?? $activity->description,
                'tanda_tangan' => $activity->tanda_tangan,
                'activity_date' => Carbon::parse($activity->activity_date)->format('Y-m-d'),
                'status' => $activity->status,
            ],
        ]);
    }

    public function update(UpdateActivityRequest $request, int $id): RedirectResponse
    {
        $activity = $this->getScopedActivityUseCase->execute($id, 'desa');
        $this->authorize('update', $activity);
        $this->updateActivityAction->execute($activity, $request->validated());

        return redirect()->route('desa.activities.index')->with('success', 'Kegiatan berhasil diperbarui');
    }

    public function destroy(int $id): RedirectResponse
    {
        $activity = $this->getScopedActivityUseCase->execute($id, 'desa');
        $this->authorize('delete', $activity);
        $this->activityRepository->delete($activity);

        return redirect()->route('desa.activities.index')->with('success', 'Kegiatan berhasil dihapus');
    }
}
