<?php

namespace App\Policies;

use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\Activities\Services\ActivityScopeService;
use App\Models\User;

class ActivityPolicy
{
    public function __construct(
        private readonly ActivityScopeService $activityScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return $this->activityScopeService->canEnterModule($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function view(User $user, Activity $activity): bool
    {
        return $this->activityScopeService->canView($user, $activity);
    }

    public function update(User $user, Activity $activity): bool
    {
        return $this->activityScopeService->canUpdate($user, $activity);
    }

    public function delete(User $user, Activity $activity): bool
    {
        return $this->update($user, $activity);
    }

    public function print(User $user, Activity $activity): bool
    {
        return $this->activityScopeService->canPrint($user, $activity);
    }
}
