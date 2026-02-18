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

class KecamatanActivityController extends Controller
{
    public function __construct(
        private readonly ActivityRepositoryInterface $activityRepository,
        private readonly ListScopedActivitiesUseCase $listScopedActivitiesUseCase,
        private readonly GetScopedActivityUseCase $getScopedActivityUseCase,
        private readonly CreateScopedActivityAction $createScopedActivityAction,
        private readonly UpdateActivityAction $updateActivityAction
    ) {
        $this->middleware('scope.role:kecamatan');
    }

    public function index()
    {
        $this->authorize('viewAny', Activity::class);
        $activities = $this->listScopedActivitiesUseCase->execute('kecamatan');

        return view('kecamatan.activities.index', compact('activities'));
    }

    public function create()
    {
        $this->authorize('create', Activity::class);
        return view('kecamatan.activities.create');
    }

    public function store(StoreActivityRequest $request)
    {
        $this->authorize('create', Activity::class);
        $this->createScopedActivityAction->execute($request->validated(), 'kecamatan');

        return redirect('/kecamatan/activities');
    }

    public function show(int $id)
    {
        $activity = $this->getScopedActivityUseCase->execute($id, 'kecamatan');
        $this->authorize('view', $activity);

        return view('kecamatan.activities.show', compact('activity'));
    }

    public function edit(int $id)
    {
        $activity = $this->getScopedActivityUseCase->execute($id, 'kecamatan');
        $this->authorize('update', $activity);

        return view('kecamatan.activities.edit', compact('activity'));
    }

    public function update(UpdateActivityRequest $request, int $id)
    {
        $activity = $this->getScopedActivityUseCase->execute($id, 'kecamatan');
        $this->authorize('update', $activity);
        $this->updateActivityAction->execute($activity, $request->validated());

        return redirect('/kecamatan/activities');
    }

    public function destroy(int $id)
    {
        $activity = $this->getScopedActivityUseCase->execute($id, 'kecamatan');
        $this->authorize('delete', $activity);
        $this->activityRepository->delete($activity);

        return redirect('/kecamatan/activities');
    }
}

