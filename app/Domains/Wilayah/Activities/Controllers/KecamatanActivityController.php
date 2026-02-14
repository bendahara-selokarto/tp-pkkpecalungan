<?php

namespace App\Domains\Wilayah\Activities\Controllers;

use App\Domains\Wilayah\Activities\DTOs\ActivityData;
use App\Domains\Wilayah\Activities\Repositories\ActivityRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KecamatanActivityController extends Controller
{
    public function __construct(
        private readonly ActivityRepository $activityRepository
    ) {
        $this->middleware('role:kecamatan');
    }

    public function index()
    {
        $user = auth()->user();
        $activities = $this->activityRepository->getByLevelAndArea('kecamatan', $user->area_id);

        return view('kecamatan.activities.index', compact('activities'));
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
            'area_id' => auth()->user()->area_id,
            'created_by' => auth()->id(),
            'activity_date' => $validated['activity_date'],
            'status' => 'draft',
        ]);

        $this->activityRepository->store($data);

        return redirect()->back();
    }
}
