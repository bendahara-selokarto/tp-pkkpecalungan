<?php

namespace App\Domains\Wilayah\Activities\Controllers;

use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\Activities\Requests\ListKecamatanDesaActivitiesRequest;
use App\Domains\Wilayah\Activities\Services\ActivityScopeService;
use App\Domains\Wilayah\Activities\UseCases\GetKecamatanDesaActivityUseCase;
use App\Domains\Wilayah\Activities\UseCases\ListKecamatanDesaActivitiesUseCase;
use App\Domains\Wilayah\Repositories\AreaRepositoryInterface;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class KecamatanDesaActivityController extends Controller
{
    public function __construct(
        private readonly ListKecamatanDesaActivitiesUseCase $listKecamatanDesaActivitiesUseCase,
        private readonly GetKecamatanDesaActivityUseCase $getKecamatanDesaActivityUseCase,
        private readonly ActivityScopeService $activityScopeService,
        private readonly AreaRepositoryInterface $areaRepository
    ) {
        $this->middleware('scope.role:kecamatan');
    }

    public function index(ListKecamatanDesaActivitiesRequest $request): Response
    {
        $this->authorize('viewAny', Activity::class);

        $kecamatanAreaId = $this->activityScopeService->requireUserAreaId();
        $activities = $this->listKecamatanDesaActivitiesUseCase
            ->execute(
                $request->perPage(),
                $request->desaId(),
                $request->status(),
                $request->keyword()
            )
            ->through(fn (Activity $activity) => [
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
                'image_path' => $activity->image_path,
                'image_url' => $this->resolveAttachmentUrl($activity, 'image'),
                'document_path' => $activity->document_path,
                'document_url' => $this->resolveAttachmentUrl($activity, 'document'),
                'area' => $activity->area
                    ? [
                        'id' => $activity->area->id,
                        'name' => $activity->area->name,
                    ]
                    : null,
                'creator' => $activity->creator
                    ? [
                        'id' => $activity->creator->id,
                        'name' => $activity->creator->name,
                    ]
                    : null,
            ]);

        $desaOptions = $this->areaRepository
            ->getDesaByKecamatan($kecamatanAreaId)
            ->map(static fn ($area) => [
                'id' => (int) $area->id,
                'name' => (string) $area->name,
            ])
            ->values();

        return Inertia::render('Kecamatan/DesaActivities/Index', [
            'activities' => $activities,
            'desaOptions' => $desaOptions,
            'statusOptions' => [
                ['value' => 'draft', 'label' => 'Draft'],
                ['value' => 'published', 'label' => 'Published'],
            ],
            'pagination' => [
                'perPageOptions' => [10, 25, 50],
            ],
            'filters' => [
                'per_page' => $request->perPage(),
                'desa_id' => $request->desaId(),
                'status' => $request->status(),
                'q' => $request->keyword(),
            ],
        ]);
    }

    public function show(int $id): Response
    {
        $activity = $this->getKecamatanDesaActivityUseCase->execute($id);
        $this->authorize('view', $activity);

        return Inertia::render('Kecamatan/DesaActivities/Show', [
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
                'image_path' => $activity->image_path,
                'image_url' => $this->resolveAttachmentUrl($activity, 'image'),
                'document_path' => $activity->document_path,
                'document_url' => $this->resolveAttachmentUrl($activity, 'document'),
                'area' => $activity->area
                    ? [
                        'id' => $activity->area->id,
                        'name' => $activity->area->name,
                    ]
                    : null,
                'creator' => $activity->creator
                    ? [
                        'id' => $activity->creator->id,
                        'name' => $activity->creator->name,
                    ]
                    : null,
            ],
            'can' => [
                'print' => auth()->user()->can('print', $activity),
            ],
            'routes' => [
                'print' => route('kecamatan.desa-activities.print', $activity->id),
            ],
        ]);
    }

    public function attachment(int $id, string $type): StreamedResponse
    {
        $activity = $this->getKecamatanDesaActivityUseCase->execute($id);
        $this->authorize('view', $activity);

        $path = match ($type) {
            'image' => $activity->image_path,
            'document' => $activity->document_path,
            default => null,
        };

        if (! is_string($path) || $path === '' || ! Storage::disk('public')->exists($path)) {
            abort(404);
        }

        return Storage::disk('public')->response($path, basename($path), [
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private function resolveAttachmentUrl(Activity $activity, string $type): ?string
    {
        $path = match ($type) {
            'image' => $activity->image_path,
            'document' => $activity->document_path,
            default => null,
        };

        if (! is_string($path) || $path === '') {
            return null;
        }

        return route('kecamatan.desa-activities.attachments.show', [
            'id' => $activity->id,
            'type' => $type,
        ]);
    }
}
