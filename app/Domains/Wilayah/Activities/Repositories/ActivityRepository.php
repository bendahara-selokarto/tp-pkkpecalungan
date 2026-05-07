<?php

namespace App\Domains\Wilayah\Activities\Repositories;

use App\Domains\Wilayah\Activities\DTOs\ActivityData;
use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\Activities\Services\ActivityScopeService;
use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Domains\Wilayah\Repositories\AreaRepositoryInterface;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ActivityRepository implements ActivityRepositoryInterface
{
    public function __construct(
        private readonly AreaRepositoryInterface $areaRepository,
        private readonly ActivityScopeService $activityScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {}

    public function store(ActivityData $data): Activity
    {
        return Activity::create([
            'title' => $data->title,
            'nama_petugas' => $data->nama_petugas,
            'jabatan_petugas' => $data->jabatan_petugas,
            'description' => $data->description,
            'uraian' => $data->uraian,
            'level' => $data->level,
            'group' => $data->group,
            'area_id' => $data->area_id,
            'created_by' => $data->created_by,
            'tahun_anggaran' => $data->tahun_anggaran,
            'activity_date' => $data->activity_date,
            'tempat_kegiatan' => $data->tempat_kegiatan,
            'status' => $data->status,
            'tanda_tangan' => $data->tanda_tangan,
            'image_path' => $data->image_path,
            'document_path' => $data->document_path,
        ]);
    }

    public function paginateByLevelAndArea(
        string $level,
        int $areaId,
        int $tahunAnggaran,
        int $perPage,
        ?User $actor = null
    ): LengthAwarePaginator {
        $query = Activity::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->where('tahun_anggaran', $tahunAnggaran);

        $query = $this->applyActivityGroupFilter($query, $actor);

        return $query
            ->latest('activity_date')
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function listByLevelAndArea(
        string $level,
        int $areaId,
        int $tahunAnggaran,
        ?User $actor = null
    ): Collection {
        $query = Activity::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->where('tahun_anggaran', $tahunAnggaran);

        $query = $this->applyActivityGroupFilter($query, $actor);

        return $query
            ->latest('activity_date')
            ->latest('id')
            ->get();
    }

    public function paginateDesaActivitiesByKecamatan(
        int $kecamatanAreaId,
        int $tahunAnggaran,
        int $perPage,
        ?int $desaId = null,
        ?string $status = null,
        ?string $keyword = null
    ): LengthAwarePaginator {
        $desaIds = $this->areaRepository
            ->getDesaByKecamatan($kecamatanAreaId)
            ->pluck('id');

        $query = Activity::query()
            ->with(['area', 'creator'])
            ->where('level', ScopeLevel::DESA->value)
            ->where('tahun_anggaran', $tahunAnggaran)
            ->whereIn('area_id', $desaIds);

        if (is_int($desaId)) {
            $query->where('area_id', $desaId);
        }

        if (is_string($status) && $status !== '') {
            $query->where('status', $status);
        }

        if (is_string($keyword) && $keyword !== '') {
            $normalizedKeyword = trim($keyword);
            $query->where(static function (Builder $inner) use ($normalizedKeyword): void {
                $inner
                    ->where('title', 'like', '%'.$normalizedKeyword.'%')
                    ->orWhere('description', 'like', '%'.$normalizedKeyword.'%')
                    ->orWhere('nama_petugas', 'like', '%'.$normalizedKeyword.'%')
                    ->orWhere('tempat_kegiatan', 'like', '%'.$normalizedKeyword.'%');
            });
        }

        return $query
            ->latest('activity_date')
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function queryScopedByUser(User $user): Builder
    {
        $query = Activity::query();

        if ($user->hasRole('super-admin')) {
            return $query->where('tahun_anggaran', $this->activeBudgetYearContextService->resolveForUser($user));
        }

        if (! is_numeric($user->area_id)) {
            return $query->whereRaw('1 = 0');
        }

        $areaId = (int) $user->area_id;
        $tahunAnggaran = $this->activeBudgetYearContextService->resolveForUser($user);
        $areaLevel = $user->relationLoaded('area')
            ? $user->area?->level
            : $this->areaRepository->getLevelById($areaId);

        if (
            $user->hasRoleForScope(ScopeLevel::DESA->value)
            && $areaLevel === ScopeLevel::DESA->value
        ) {
            return $this->applyActivityGroupFilter(
                $query
                    ->where('level', ScopeLevel::DESA->value)
                    ->where('area_id', $areaId)
                    ->where('tahun_anggaran', $tahunAnggaran),
                $user
            );
        }

        if (
            $user->hasRoleForScope(ScopeLevel::KECAMATAN->value)
            && $areaLevel === ScopeLevel::KECAMATAN->value
        ) {
            $desaIds = $this->areaRepository
                ->getDesaByKecamatan($areaId)
                ->pluck('id');

            $allowedGroups = $this->activityScopeService->requiresActivityGroupFilter($user)
                ? $this->activityScopeService->resolveActivityGroupsForUser($user)
                : [];

            if ($this->activityScopeService->requiresActivityGroupFilter($user) && $allowedGroups === []) {
                return $query->whereRaw('1 = 0');
            }

            return $query->where(function (Builder $scoped) use ($areaId, $desaIds, $tahunAnggaran, $allowedGroups) {
                $scoped->where(function (Builder $kecamatanScope) use ($areaId, $tahunAnggaran, $allowedGroups) {
                    $kecamatanScope
                        ->where('level', ScopeLevel::KECAMATAN->value)
                        ->where('area_id', $areaId)
                        ->where('tahun_anggaran', $tahunAnggaran);

                    if ($allowedGroups !== []) {
                        $kecamatanScope->whereIn('group', $allowedGroups);
                    }
                })->orWhere(function (Builder $desaScope) use ($desaIds, $tahunAnggaran) {
                    $desaScope
                        ->where('level', ScopeLevel::DESA->value)
                        ->where('tahun_anggaran', $tahunAnggaran)
                        ->whereIn('area_id', $desaIds);
                });
            });
        }

        return $query->whereRaw('1 = 0');
    }

    public function find(int $id): Activity
    {
        return Activity::findOrFail($id);
    }

    public function update(Activity $activity, ActivityData $data): Activity
    {
        $activity->update([
            'title' => $data->title,
            'nama_petugas' => $data->nama_petugas,
            'jabatan_petugas' => $data->jabatan_petugas,
            'description' => $data->description,
            'uraian' => $data->uraian,
            'tahun_anggaran' => $data->tahun_anggaran,
            'activity_date' => $data->activity_date,
            'tempat_kegiatan' => $data->tempat_kegiatan,
            'status' => $data->status,
            'tanda_tangan' => $data->tanda_tangan,
            'image_path' => $data->image_path,
            'document_path' => $data->document_path,
        ]);

        return $activity;
    }

    public function delete(Activity $activity): void
    {
        $activity->delete();
    }

    private function applyActivityGroupFilter(Builder $query, ?User $actor): Builder
    {
        if (! $actor instanceof User) {
            return $query;
        }

        if (! $this->activityScopeService->requiresActivityGroupFilter($actor)) {
            return $query;
        }

        $allowedGroups = $this->activityScopeService->resolveActivityGroupsForUser($actor);
        if ($allowedGroups === []) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn('group', $allowedGroups);
    }
}
