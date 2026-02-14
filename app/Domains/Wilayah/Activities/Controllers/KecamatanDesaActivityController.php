<?php

namespace App\Domains\Wilayah\Activities\Controllers;

use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\Activities\Repositories\ActivityRepository;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpKernel\Exception\HttpException;

class KecamatanDesaActivityController extends Controller
{
    public function __construct(
        private readonly ActivityRepository $activityRepository
    ) {
        $this->middleware('role:admin-kecamatan');
    }

    public function index()
    {
        $kecamatanAreaId = $this->requireUserAreaId();
        $activities = $this->activityRepository->getDesaActivitiesByKecamatan($kecamatanAreaId);

        return view('kecamatan.desa-activities.index', compact('activities'));
    }

    public function show(int $id)
    {
        $activity = $this->getAuthorizedDesaActivity($id);

        return view('kecamatan.desa-activities.show', compact('activity'));
    }

    private function getAuthorizedDesaActivity(int $id): Activity
    {
        $activity = $this->activityRepository->find($id);
        $kecamatanAreaId = $this->requireUserAreaId();

        if (
            $activity->level !== 'desa'
            || $activity->area?->level !== 'desa'
            || $activity->area?->parent_id !== $kecamatanAreaId
        ) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $activity->loadMissing(['area', 'creator']);
    }

    private function requireUserAreaId(): int
    {
        $areaId = auth()->user()?->area_id;

        if (! is_numeric($areaId)) {
            throw new HttpException(403, 'Area pengguna belum ditentukan.');
        }

        return (int) $areaId;
    }
}
