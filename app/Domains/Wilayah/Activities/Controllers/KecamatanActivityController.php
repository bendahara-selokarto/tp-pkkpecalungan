<?php

namespace App\Domains\Wilayah\Activities\Controllers;

use App\Domains\Wilayah\Activities\Actions\CreateScopedActivityAction;
use App\Domains\Wilayah\Activities\Actions\UpdateActivityAction;
use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\Activities\Requests\ListActivitiesRequest;
use App\Domains\Wilayah\Activities\Requests\StoreActivityRequest;
use App\Domains\Wilayah\Activities\Requests\UpdateActivityRequest;
use App\Domains\Wilayah\Activities\Repositories\ActivityRepositoryInterface;
use App\Domains\Wilayah\Activities\Services\ActivityAttachmentService;
use App\Domains\Wilayah\Activities\UseCases\GetScopedActivityUseCase;
use App\Domains\Wilayah\Activities\UseCases\ListScopedActivitiesUseCase;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class KecamatanActivityController extends Controller
{
    public function __construct(
        private readonly ActivityRepositoryInterface $activityRepository,
        private readonly ListScopedActivitiesUseCase $listScopedActivitiesUseCase,
        private readonly GetScopedActivityUseCase $getScopedActivityUseCase,
        private readonly CreateScopedActivityAction $createScopedActivityAction,
        private readonly UpdateActivityAction $updateActivityAction,
        private readonly ActivityAttachmentService $activityAttachmentService
    ) {
        $this->middleware('scope.role:kecamatan');
    }

    public function index(ListActivitiesRequest $request): Response
    {
        $this->authorize('viewAny', Activity::class);
        $activities = $this->listScopedActivitiesUseCase
            ->execute('kecamatan', $request->perPage())
            ->through(fn (Activity $activity) => $this->mapActivityPayload($activity));

        return Inertia::render('Kecamatan/Activities/Index', [
            'activities' => $activities,
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
        $this->authorize('create', Activity::class);

        return Inertia::render('Kecamatan/Activities/Create');
    }

    public function store(StoreActivityRequest $request): RedirectResponse
    {
        $this->authorize('create', Activity::class);
        $this->createScopedActivityAction->execute($request->validated(), 'kecamatan');

        return redirect()->route('kecamatan.activities.index')->with('success', 'Kegiatan berhasil dibuat');
    }

    public function show(int $id): Response
    {
        $activity = $this->getScopedActivityUseCase->execute($id, 'kecamatan');
        $this->authorize('view', $activity);

        return Inertia::render('Kecamatan/Activities/Show', [
            'activity' => $this->mapActivityPayload($activity),
            'can' => [
                'print' => auth()->user()->can('print', $activity),
            ],
            'routes' => [
                'print' => route('kecamatan.activities.print', $activity->id),
            ],
        ]);
    }

    public function edit(int $id): Response
    {
        $activity = $this->getScopedActivityUseCase->execute($id, 'kecamatan');
        $this->authorize('update', $activity);

        return Inertia::render('Kecamatan/Activities/Edit', [
            'activity' => $this->mapActivityPayload($activity),
        ]);
    }

    public function update(UpdateActivityRequest $request, int $id): RedirectResponse
    {
        $activity = $this->getScopedActivityUseCase->execute($id, 'kecamatan');
        $this->authorize('update', $activity);
        $this->updateActivityAction->execute($activity, $request->validated());

        return redirect()->route('kecamatan.activities.index')->with('success', 'Kegiatan berhasil diperbarui');
    }

    public function destroy(int $id): RedirectResponse
    {
        $activity = $this->getScopedActivityUseCase->execute($id, 'kecamatan');
        $this->authorize('delete', $activity);
        $this->activityAttachmentService->deleteForActivity($activity);
        $this->activityRepository->delete($activity);

        return redirect()->route('kecamatan.activities.index')->with('success', 'Kegiatan berhasil dihapus');
    }

    /**
     * @return array<string, mixed>
     */
    private function mapActivityPayload(Activity $activity): array
    {
        return [
            'id' => $activity->id,
            'title' => $activity->title,
            'description' => $activity->description,
            'nama_petugas' => $activity->nama_petugas ?? $activity->title,
            'jabatan_petugas' => $activity->jabatan_petugas,
            'tempat_kegiatan' => $activity->tempat_kegiatan,
            'uraian' => $activity->uraian ?? $activity->description,
            'tanda_tangan' => $activity->tanda_tangan,
            'activity_date' => $this->formatDateForPayload($activity->activity_date),
            'status' => $activity->status,
            'image_path' => $activity->image_path,
            'image_url' => $this->resolvePublicUrl($activity->image_path),
            'document_path' => $activity->document_path,
            'document_url' => $this->resolvePublicUrl($activity->document_path),
        ];
    }

    private function formatDateForPayload(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        return Carbon::parse($value)->format('Y-m-d');
    }

    private function resolvePublicUrl(?string $path): ?string
    {
        if (! is_string($path) || $path === '') {
            return null;
        }

        return Storage::disk('public')->url($path);
    }
}
