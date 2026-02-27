<?php

namespace App\Domains\Wilayah\Activities\Repositories;

use App\Domains\Wilayah\Activities\DTOs\ActivityData;
use App\Domains\Wilayah\Activities\Models\Activity;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ActivityRepositoryInterface
{
    public function store(ActivityData $data): Activity;

    public function paginateByLevelAndArea(string $level, int $areaId, int $perPage, ?User $actor = null): LengthAwarePaginator;

    public function listByLevelAndArea(string $level, int $areaId, ?User $actor = null): Collection;

    public function paginateDesaActivitiesByKecamatan(int $kecamatanAreaId, int $perPage): LengthAwarePaginator;

    public function queryScopedByUser(User $user): Builder;

    public function find(int $id): Activity;

    public function update(Activity $activity, ActivityData $data): Activity;

    public function delete(Activity $activity): void;
}
