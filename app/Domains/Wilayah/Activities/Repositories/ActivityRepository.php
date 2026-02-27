<?php

namespace App\Domains\Wilayah\Activities\Repositories;

use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\Activities\DTOs\ActivityData;
use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Domains\Wilayah\Repositories\AreaRepositoryInterface;
use App\Domains\Wilayah\Activities\Services\ActivityScopeService;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ActivityRepository implements ActivityRepositoryInterface
{
    public function __construct(
        private readonly AreaRepositoryInterface $areaRepository,
        private readonly ActivityScopeService $activityScopeService
    ) {
    }

    public function store(ActivityData $data): Activity
    {
        return Activity::create([
            'title'         => $data->title,
            'nama_petugas'  => $data->nama_petugas,
            'jabatan_petugas' => $data->jabatan_petugas,
            'description'   => $data->description,
            'uraian'        => $data->uraian,
            'level'         => $data->level,
            'area_id'       => $data->area_id,
            'created_by'    => $data->created_by,
            'activity_date' => $data->activity_date,
            'tempat_kegiatan' => $data->tempat_kegiatan,
            'status'        => $data->status,
            'tanda_tangan'  => $data->tanda_tangan,
            'image_path'    => $data->image_path,
            'document_path' => $data->document_path,
        ]);
    }

    public function paginateByLevelAndArea(
        string $level,
        int $areaId,
        int $perPage,
        ?User $actor = null,
        ?int $creatorIdFilter = null
    ): LengthAwarePaginator
    {
        $query = Activity::query()
            ->where('level', $level)
            ->where('area_id', $areaId);

        if (is_int($creatorIdFilter)) {
            $query->where('created_by', $creatorIdFilter);
        }

        $query = $this->applyRoleScopedCreatorFilter($query, $actor, $level);

        return $query
            ->latest('activity_date')
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function listByLevelAndArea(
        string $level,
        int $areaId,
        ?User $actor = null,
        ?int $creatorIdFilter = null
    ): Collection
    {
        $query = Activity::query()
            ->where('level', $level)
            ->where('area_id', $areaId);

        if (is_int($creatorIdFilter)) {
            $query->where('created_by', $creatorIdFilter);
        }

        $query = $this->applyRoleScopedCreatorFilter($query, $actor, $level);

        return $query
            ->latest('activity_date')
            ->latest('id')
            ->get();
    }

    public function paginateDesaActivitiesByKecamatan(
        int $kecamatanAreaId,
        int $perPage,
        ?int $desaId = null,
        ?string $status = null,
        ?string $keyword = null
    ): LengthAwarePaginator
    {
        $desaIds = $this->areaRepository
            ->getDesaByKecamatan($kecamatanAreaId)
            ->pluck('id');

        $query = Activity::query()
            ->with(['area', 'creator'])
            ->where('level', ScopeLevel::DESA->value)
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
                    ->where('title', 'like', '%' . $normalizedKeyword . '%')
                    ->orWhere('description', 'like', '%' . $normalizedKeyword . '%')
                    ->orWhere('nama_petugas', 'like', '%' . $normalizedKeyword . '%')
                    ->orWhere('tempat_kegiatan', 'like', '%' . $normalizedKeyword . '%');
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
            return $query;
        }

        if (! is_numeric($user->area_id)) {
            return $query->whereRaw('1 = 0');
        }

        $areaId = (int) $user->area_id;
        $areaLevel = $user->relationLoaded('area')
            ? $user->area?->level
            : $this->areaRepository->getLevelById($areaId);

        if (
            $user->hasRoleForScope(ScopeLevel::DESA->value)
            && $areaLevel === ScopeLevel::DESA->value
        ) {
            return $this->applyRoleScopedCreatorFilter(
                $query
                ->where('level', ScopeLevel::DESA->value)
                ->where('area_id', $areaId),
                $user,
                ScopeLevel::DESA->value
            );
        }

        if (
            $user->hasRoleForScope(ScopeLevel::KECAMATAN->value)
            && $areaLevel === ScopeLevel::KECAMATAN->value
        ) {
            $desaIds = $this->areaRepository
                ->getDesaByKecamatan($areaId)
                ->pluck('id');

            return $query->where(function (Builder $scoped) use ($areaId, $desaIds) {
                $scoped->where(function (Builder $kecamatanScope) use ($areaId) {
                    $kecamatanScope
                        ->where('level', ScopeLevel::KECAMATAN->value)
                        ->where('area_id', $areaId);
                })->orWhere(function (Builder $desaScope) use ($desaIds) {
                    $desaScope
                        ->where('level', ScopeLevel::DESA->value)
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
            'title'         => $data->title,
            'nama_petugas' => $data->nama_petugas,
            'jabatan_petugas' => $data->jabatan_petugas,
            'description'   => $data->description,
            'uraian'        => $data->uraian,
            'activity_date' => $data->activity_date,
            'tempat_kegiatan' => $data->tempat_kegiatan,
            'status'        => $data->status,
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

    private function applyRoleScopedCreatorFilter(Builder $query, ?User $actor, string $level): Builder
    {
        if (! $actor instanceof User) {
            return $query;
        }

        if (! $this->activityScopeService->requiresRoleScopedActivityFilter($actor, $level)) {
            return $query;
        }

        $allowedRoles = $this->activityScopeService->resolveRoleScopedActivityRoles($actor, $level);
        if ($allowedRoles === []) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereHas('creator.roles', static function (Builder $rolesQuery) use ($allowedRoles): void {
            $rolesQuery->whereIn('name', $allowedRoles);
        });
    }
}
