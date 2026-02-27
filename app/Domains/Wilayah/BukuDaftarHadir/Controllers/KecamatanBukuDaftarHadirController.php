<?php

namespace App\Domains\Wilayah\BukuDaftarHadir\Controllers;

use App\Domains\Wilayah\BukuDaftarHadir\Actions\CreateScopedBukuDaftarHadirAction;
use App\Domains\Wilayah\BukuDaftarHadir\Actions\UpdateBukuDaftarHadirAction;
use App\Domains\Wilayah\BukuDaftarHadir\Models\BukuDaftarHadir;
use App\Domains\Wilayah\BukuDaftarHadir\Repositories\BukuDaftarHadirRepositoryInterface;
use App\Domains\Wilayah\BukuDaftarHadir\Requests\ListBukuDaftarHadirRequest;
use App\Domains\Wilayah\BukuDaftarHadir\Requests\StoreBukuDaftarHadirRequest;
use App\Domains\Wilayah\BukuDaftarHadir\Requests\UpdateBukuDaftarHadirRequest;
use App\Domains\Wilayah\BukuDaftarHadir\Services\BukuDaftarHadirScopeService;
use App\Domains\Wilayah\BukuDaftarHadir\UseCases\GetScopedBukuDaftarHadirUseCase;
use App\Domains\Wilayah\BukuDaftarHadir\UseCases\ListScopedBukuDaftarHadirUseCase;
use App\Http\Controllers\Controller;
use App\Domains\Wilayah\Activities\Models\Activity;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class KecamatanBukuDaftarHadirController extends Controller
{
    public function __construct(
        private readonly BukuDaftarHadirRepositoryInterface $bukuDaftarHadirRepository,
        private readonly ListScopedBukuDaftarHadirUseCase $listScopedBukuDaftarHadirUseCase,
        private readonly GetScopedBukuDaftarHadirUseCase $getScopedBukuDaftarHadirUseCase,
        private readonly CreateScopedBukuDaftarHadirAction $createScopedBukuDaftarHadirAction,
        private readonly UpdateBukuDaftarHadirAction $updateBukuDaftarHadirAction,
        private readonly BukuDaftarHadirScopeService $bukuDaftarHadirScopeService
    ) {
        $this->middleware('scope.role:kecamatan');
    }

    public function index(ListBukuDaftarHadirRequest $request): Response
    {
        $this->authorize('viewAny', BukuDaftarHadir::class);
        $items = $this->listScopedBukuDaftarHadirUseCase
            ->execute('kecamatan', $request->perPage())
            ->through(fn (BukuDaftarHadir $item) => [
                'id' => $item->id,
                'attendance_date' => $this->formatDateForPayload($item->attendance_date),
                'activity_id' => $item->activity_id,
                'activity_title' => $item->activity?->title,
                'attendee_name' => $item->attendee_name,
                'institution' => $item->institution,
                'description' => $item->description,
            ]);

        return Inertia::render('Kecamatan/BukuDaftarHadir/Index', [
            'items' => $items,
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
        $this->authorize('create', BukuDaftarHadir::class);

        return Inertia::render('Kecamatan/BukuDaftarHadir/Create', [
            'activityOptions' => $this->activityOptions('kecamatan'),
        ]);
    }

    public function store(StoreBukuDaftarHadirRequest $request): RedirectResponse
    {
        $this->authorize('create', BukuDaftarHadir::class);
        $this->createScopedBukuDaftarHadirAction->execute($request->validated(), 'kecamatan');

        return redirect()
            ->route('kecamatan.buku-daftar-hadir.index')
            ->with('success', 'Buku Daftar Hadir berhasil dibuat');
    }

    public function show(int $id): Response
    {
        $item = $this->getScopedBukuDaftarHadirUseCase->execute($id, 'kecamatan');
        $this->authorize('view', $item);

        return Inertia::render('Kecamatan/BukuDaftarHadir/Show', [
            'item' => [
                'id' => $item->id,
                'attendance_date' => $this->formatDateForPayload($item->attendance_date),
                'activity_id' => $item->activity_id,
                'activity_title' => $item->activity?->title,
                'activity_date' => $this->formatDateForPayload($item->activity?->activity_date),
                'attendee_name' => $item->attendee_name,
                'institution' => $item->institution,
                'description' => $item->description,
            ],
        ]);
    }

    public function edit(int $id): Response
    {
        $item = $this->getScopedBukuDaftarHadirUseCase->execute($id, 'kecamatan');
        $this->authorize('update', $item);

        return Inertia::render('Kecamatan/BukuDaftarHadir/Edit', [
            'item' => [
                'id' => $item->id,
                'attendance_date' => $this->formatDateForPayload($item->attendance_date),
                'activity_id' => $item->activity_id,
                'activity_title' => $item->activity?->title,
                'activity_date' => $this->formatDateForPayload($item->activity?->activity_date),
                'attendee_name' => $item->attendee_name,
                'institution' => $item->institution,
                'description' => $item->description,
            ],
            'activityOptions' => $this->activityOptions('kecamatan'),
        ]);
    }

    public function update(UpdateBukuDaftarHadirRequest $request, int $id): RedirectResponse
    {
        $item = $this->getScopedBukuDaftarHadirUseCase->execute($id, 'kecamatan');
        $this->authorize('update', $item);
        $this->updateBukuDaftarHadirAction->execute($item, $request->validated());

        return redirect()
            ->route('kecamatan.buku-daftar-hadir.index')
            ->with('success', 'Buku Daftar Hadir berhasil diperbarui');
    }

    public function destroy(int $id): RedirectResponse
    {
        $item = $this->getScopedBukuDaftarHadirUseCase->execute($id, 'kecamatan');
        $this->authorize('delete', $item);
        $this->bukuDaftarHadirRepository->delete($item);

        return redirect()
            ->route('kecamatan.buku-daftar-hadir.index')
            ->with('success', 'Buku Daftar Hadir berhasil dihapus');
    }

    private function formatDateForPayload(mixed $value): ?string
    {
        if (! $value) {
            return null;
        }

        return Carbon::parse((string) $value)->format('Y-m-d');
    }

    private function activityOptions(string $level): array
    {
        $areaId = $this->bukuDaftarHadirScopeService->requireUserAreaId();

        return $this->bukuDaftarHadirRepository
            ->listActivityOptionsByLevelAndArea($level, $areaId)
            ->map(fn (Activity $activity) => [
                'id' => $activity->id,
                'title' => $activity->title,
                'activity_date' => $this->formatDateForPayload($activity->activity_date),
            ])
            ->values()
            ->all();
    }
}
