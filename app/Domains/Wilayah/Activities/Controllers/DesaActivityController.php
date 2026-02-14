<?php

namespace App\Domains\Wilayah\Activities\Controllers;

use App\Domains\Wilayah\Activities\Actions\CreateScopedActivityAction;
use App\Domains\Wilayah\Activities\Actions\UpdateActivityAction;
use App\Domains\Wilayah\Activities\Requests\StoreActivityRequest;
use App\Domains\Wilayah\Activities\Requests\UpdateActivityRequest;
use App\Domains\Wilayah\Activities\Repositories\ActivityRepository;
use App\Domains\Wilayah\Activities\UseCases\GetScopedActivityUseCase;
use App\Domains\Wilayah\Activities\UseCases\ListScopedActivitiesUseCase;
use App\Http\Controllers\Controller;

class DesaActivityController extends Controller
{
    public function __construct(
        private readonly ActivityRepository $activityRepository,
        private readonly ListScopedActivitiesUseCase $listScopedActivitiesUseCase,
        private readonly GetScopedActivityUseCase $getScopedActivityUseCase,
        private readonly CreateScopedActivityAction $createScopedActivityAction,
        private readonly UpdateActivityAction $updateActivityAction
    ) {
        $this->middleware('role:admin-desa');
    }

    public function index()
    {
        $activities = $this->listScopedActivitiesUseCase->execute('desa');

        return view('desa.activities.index', compact('activities'));
    }

    public function create()
    {
        return view('desa.activities.create');
    }

    public function store(StoreActivityRequest $request)
    {
        $this->createScopedActivityAction->execute($request->validated(), 'desa');

        return redirect('/desa/activities');
    }

    public function show(int $id)
    {
        $activity = $this->getScopedActivityUseCase->execute($id, 'desa');

        return view('desa.activities.show', compact('activity'));
    }

    public function edit(int $id)
    {
        $activity = $this->getScopedActivityUseCase->execute($id, 'desa');

        return view('desa.activities.edit', compact('activity'));
    }

    public function update(UpdateActivityRequest $request, int $id)
    {
        $activity = $this->getScopedActivityUseCase->execute($id, 'desa');
        $this->updateActivityAction->execute($activity, $request->validated());

        return redirect('/desa/activities');
    }

    public function destroy(int $id)
    {
        $activity = $this->getScopedActivityUseCase->execute($id, 'desa');
        $this->activityRepository->delete($activity);

        return redirect('/desa/activities');
    }
}
