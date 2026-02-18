<?php

namespace App\Domains\Wilayah\Activities\Repositories;

use App\Domains\Wilayah\Activities\DTOs\ActivityData;
use App\Domains\Wilayah\Activities\Models\Activity;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

interface ActivityRepositoryInterface
{
    public function store(ActivityData $data): Activity;

    public function getByLevelAndArea(string $level, int $areaId): Collection;

    public function getDesaActivitiesByKecamatan(int $kecamatanAreaId): Collection;

    public function queryScopedByUser(User $user): Builder;

    public function find(int $id): Activity;

    public function update(Activity $activity, ActivityData $data): Activity;

    public function delete(Activity $activity): void;
}

