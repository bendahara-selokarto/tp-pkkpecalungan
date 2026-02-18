<?php

namespace App\Domains\Wilayah\Activities\Controllers;

use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\Activities\UseCases\GetKecamatanDesaActivityUseCase;
use App\Domains\Wilayah\Activities\UseCases\ListKecamatanDesaActivitiesUseCase;
use App\Http\Controllers\Controller;

class KecamatanDesaActivityController extends Controller
{
    public function __construct(
        private readonly ListKecamatanDesaActivitiesUseCase $listKecamatanDesaActivitiesUseCase,
        private readonly GetKecamatanDesaActivityUseCase $getKecamatanDesaActivityUseCase
    ) {
        $this->middleware('scope.role:kecamatan');
    }

    public function index()
    {
        $this->authorize('viewAny', Activity::class);
        $activities = $this->listKecamatanDesaActivitiesUseCase->execute();

        return view('kecamatan.desa-activities.index', compact('activities'));
    }

    public function show(int $id)
    {
        $activity = $this->getKecamatanDesaActivityUseCase->execute($id);
        $this->authorize('view', $activity);

        return view('kecamatan.desa-activities.show', compact('activity'));
    }
}

