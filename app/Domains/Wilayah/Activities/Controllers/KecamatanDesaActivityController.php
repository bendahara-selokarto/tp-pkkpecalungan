<?php

namespace App\Domains\Wilayah\Activities\Controllers;

use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\Activities\UseCases\GetKecamatanDesaActivityUseCase;
use App\Domains\Wilayah\Activities\UseCases\ListKecamatanDesaActivitiesUseCase;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class KecamatanDesaActivityController extends Controller
{
    public function __construct(
        private readonly ListKecamatanDesaActivitiesUseCase $listKecamatanDesaActivitiesUseCase,
        private readonly GetKecamatanDesaActivityUseCase $getKecamatanDesaActivityUseCase
    ) {
        $this->middleware('scope.role:kecamatan');
    }

    public function index(): Response
    {
        $this->authorize('viewAny', Activity::class);
        $activities = $this->listKecamatanDesaActivitiesUseCase->execute();

        return Inertia::render('Kecamatan/DesaActivities/Index', [
            'activities' => $activities->map(fn (Activity $activity) => [
                'id' => $activity->id,
                'title' => $activity->title,
                'description' => $activity->description,
                'activity_date' => $activity->activity_date,
                'status' => $activity->status,
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
            ])->values(),
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
                'activity_date' => $activity->activity_date,
                'status' => $activity->status,
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
}
