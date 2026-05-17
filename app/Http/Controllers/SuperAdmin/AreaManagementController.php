<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Domains\Wilayah\Models\Area;
use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\UpdateAreaRequest;
use App\UseCases\SuperAdmin\ListAreasUseCase;
use App\UseCases\SuperAdmin\UpdateAreaUseCase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AreaManagementController extends Controller
{
    public function __construct(
        private readonly ListAreasUseCase $listAreasUseCase,
        private readonly UpdateAreaUseCase $updateAreaUseCase
    ) {}

    public function index(Request $request): Response
    {
        $perPage = $request->input('per_page', 10);
        $areas = $this->listAreasUseCase->execute($perPage);

        return Inertia::render('SuperAdmin/Areas/Index', [
            'areas' => $areas,
            'filters' => [
                'per_page' => $perPage,
            ],
        ]);
    }

    public function edit(Area $area): Response
    {
        return Inertia::render('SuperAdmin/Areas/Edit', [
            'area' => $area->load('parent'),
        ]);
    }

    public function update(UpdateAreaRequest $request, Area $area): RedirectResponse
    {
        $this->updateAreaUseCase->execute($area, $request->validated());

        return redirect()
            ->route('super-admin.areas.index')
            ->with('success', 'Data wilayah berhasil diperbarui');
    }
}
