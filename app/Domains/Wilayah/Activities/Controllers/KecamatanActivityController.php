<?php

namespace App\Domains\Wilayah\Activities\Controllers;

use App\Domains\Wilayah\Activities\DTOs\ActivityData;
use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\Activities\Repositories\ActivityRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class KecamatanActivityController extends Controller
{
    public function __construct(
        private readonly ActivityRepository $activityRepository
    ) {
        $this->middleware('role:admin-kecamatan');
    }

    public function index()
    {
        $areaId = $this->requireUserAreaId();
        $activities = $this->activityRepository->getByLevelAndArea('kecamatan', $areaId);

        return view('kecamatan.activities.index', compact('activities'));
    }

    public function create()
    {
        return view('kecamatan.activities.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'activity_date' => 'required|date',
        ]);

        $data = ActivityData::fromArray([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'level' => 'kecamatan',
            'area_id' => $this->requireUserAreaId(),
            'created_by' => auth()->id(),
            'activity_date' => $validated['activity_date'],
            'status' => 'draft',
        ]);

        $this->activityRepository->store($data);

        return redirect('/kecamatan/activities');
    }

    public function show(int $id)
    {
        $activity = $this->getAuthorizedKecamatanActivity($id);

        return view('kecamatan.activities.show', compact('activity'));
    }

    public function edit(int $id)
    {
        $activity = $this->getAuthorizedKecamatanActivity($id);

        return view('kecamatan.activities.edit', compact('activity'));
    }

    public function update(Request $request, int $id)
    {
        $activity = $this->getAuthorizedKecamatanActivity($id);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'activity_date' => 'required|date',
            'status' => 'required|in:draft,published',
        ]);

        $data = ActivityData::fromArray([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'level' => 'kecamatan',
            'area_id' => $activity->area_id,
            'created_by' => $activity->created_by,
            'activity_date' => $validated['activity_date'],
            'status' => $validated['status'],
        ]);

        $this->activityRepository->update($activity, $data);

        return redirect('/kecamatan/activities');
    }

    public function destroy(int $id)
    {
        $activity = $this->getAuthorizedKecamatanActivity($id);
        $this->activityRepository->delete($activity);

        return redirect('/kecamatan/activities');
    }

    private function getAuthorizedKecamatanActivity(int $id): Activity
    {
        $activity = $this->activityRepository->find($id);
        $areaId = $this->requireUserAreaId();

        if ($activity->level !== 'kecamatan' || $activity->area_id !== $areaId) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $activity;
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
